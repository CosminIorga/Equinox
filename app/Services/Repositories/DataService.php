<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/09/17
 * Time: 14:24
 */

namespace Equinox\Services\Repositories;


use Equinox\Definitions\Columns;
use Equinox\Models\Column;
use Equinox\Models\NamedStorage;
use Equinox\Models\Storage;
use Equinox\Models\Trigger;
use Equinox\Repositories\DataRepository;
use Equinox\Services\General\Utils;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;

class DataService
{

    /**
     * The Data repository
     * @var DataRepository
     */
    protected $dataRepository;

    /**
     * DataService constructor.
     * @param DataRepository $dataRepository
     */
    public function __construct(
        DataRepository $dataRepository
    ) {
        $this->dataRepository = $dataRepository;
    }

    /**
     * Function used to generate a new storage
     * @param NamedStorage $storage
     * @throws \Exception
     */
    public function generateStorage(NamedStorage $storage)
    {
        /* Create storage */
        $this->dataRepository->createStorageFromClosure(
            $storage->getStorageName(),
            $this->createStorageGenerator($storage)
        );

        try {
            /* Add storage triggers */
            $storage->triggers->each(function (Trigger $trigger) {
                $this->dataRepository->createTriggerFromClosure(
                    $this->createTriggerGenerator($trigger)
                );
            });

        } catch (\Exception $exception) {
            $this->dataRepository->dropStorageIfExists($storage->getStorageName());

            throw $exception;
        }

    }

    /**
     * Function used to return another function that creates the storage
     * @param NamedStorage $storage
     * @return \Closure
     */
    protected function createStorageGenerator(NamedStorage $storage): \Closure
    {
        return function (Blueprint $table) use ($storage) {
            /* Set table engine */
            $table->engine = 'InnoDB';

            /* Add the rest of the columns */
            $storage->columns->each(function (Column $columnModel) use (&$table) {
                $column = $table->addColumn(
                    $columnModel->data_type,
                    $columnModel->name,
                    $columnModel->extra
                );

                if ($columnModel->allow_null) {
                    /* @noinspection PhpUndefinedMethodInspection */
                    $column->nullable();
                }

                if ($columnModel->index) {
                    $table->{$columnModel->index}($columnModel->name);
                }
            });

            /* Add timestamp columns such as created_at, updated_at and deleted_at*/
            /* @noinspection PhpUndefinedMethodInspection */
            $table->timestamp(Columns::CREATED_AT)
                ->nullable()
                ->default(\DB::raw('CURRENT_TIMESTAMP'));
            /* @noinspection PhpUndefinedMethodInspection */
            $table->timestamp(Columns::UPDATED_AT)
                ->nullable()
                ->default(\DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));
            /* @noinspection PhpUndefinedMethodInspection */
            $table->timestamp(Columns::DELETED_AT)
                ->nullable();
        };
    }

    /**
     * Function used to return the generator function for storage triggers
     * @param Trigger $trigger
     * @return \Closure
     */
    protected function createTriggerGenerator(Trigger $trigger): \Closure
    {
        return function () use ($trigger) {
            $triggerSyntax = "CREATE TRIGGER %1\$s
            %2\$s ON %3\$s
            FOR EACH ROW
            BEGIN
                %4\$s
            END;";

            return sprintf(
                $triggerSyntax,
                $trigger->triggerName,
                $trigger->triggerType,
                $trigger->storageName,
                $trigger->triggerDefinition
            );
        };
    }


    public function modifyStorageData(): bool
    {

    }





    public function modifyStorageData_OLD(string $operation, Collection $groupedRecords, Storage $storage): bool
    {
        $step = 0;
        $batchRecords = config('general.batch_database_records');

        $baseQuery = $this->computeBaseQuery();


        /**
         * @var string $storageName
         * @var Collection $records
         */
        foreach ($groupedRecords as $storageName => $records) {
            /* Compute ON DUPLICATE clause for current storage */
            $onDuplicateClause = $this->computeOnDuplicateClause($storage);

            /* Compute current query template */
            $query = preg_replace(
                '/:duplicate_clause/',
                $onDuplicateClause,
                $baseQuery
            );

            print_r($query);
            die();
            /* Split into batches of records */
            $currentRecords = $records->nth($batchRecords, $batchRecords * $step);

            do {
                /* Add  */


                /* Increment the step */
                $step++;

                /* Take new set of records */
                $currentRecords = $records->nth($batchRecords, $batchRecords * $step);
            } while ($currentRecords->count() != 0);
        }


    }

    /**
     * Function used to return the base query for modifying storage data
     * @return string
     */
    protected function computeBaseQuery(): string
    {
        $baseQuery = <<<BASEQUERY
INSERT INTO `%s` (%s)
    VALUES %s
    ON DUPLICATE KEY UPDATE :duplicate_clause:
BASEQUERY;

        return $baseQuery;
    }

    /**
     * Function used to compute the ON DUPLICATE clause for modify query
     * @param Storage $storage
     * @return string
     */
    protected function computeOnDuplicateClause(Storage $storage): string
    {
        $intervalColumnTemplate = $this->computeIntervalColumnAggregator();

        $intervalColumnsQuery = $storage->columns->filter(function (Column $column) {
            return $column->getColumnType() == Columns::INTERVAL_COLUMN;
        })->map(function (Column $column) use ($intervalColumnTemplate) {
            $template = <<<COLUMN_TEMPLATE
:int_col: = IF(
    VALUES(:int_col:) IS NULL,
    :int_col:,
    JSON_OBJECT({$intervalColumnTemplate})
)
COLUMN_TEMPLATE;

            return preg_replace(
                '/:int_col:/',
                $column->name,
                $template
            );
        });

        return $intervalColumnsQuery->implode(', ');
    }

    /**
     * Function used to return the template used to update an interval column based on containing aggregate key
     * @return string
     */
    protected function computeIntervalColumnAggregator(): string
    {
        $intervalAggregatesConfig = $this->getAggregatesConfig();

        $elements = [];

        foreach ($intervalAggregatesConfig as $config) {
            $aggregateKey = $config['aggregate_key'];
            $aggregateFunctionTemplate = "JSON_EXTRACT(VALUES(:int_col:), '$.%1\$s')" .
                " + "  .
                "JSON_EXTRACT(:int_col:, '$.%1\$s')";

            $elements[] = Utils::quote($aggregateKey);
            $elements[] = sprintf(
                $aggregateFunctionTemplate,
                $aggregateKey
            );
        }

        return implode(', ', $elements);
    }
}