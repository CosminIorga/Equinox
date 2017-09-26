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
use Equinox\Models\Trigger;
use Equinox\Repositories\DataRepository;
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


    public function modifyStorageData(string $operation, Collection $records): bool
    {



    }

}