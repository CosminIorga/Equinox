<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 20/09/17
 * Time: 14:50
 */

namespace Equinox\Services\Structure;


use Equinox\Definitions\Data;
use Equinox\Exceptions\ColumnException;
use Equinox\Exceptions\DataException;
use Equinox\Models\Column;
use Equinox\Models\NamedStorage;
use Equinox\Models\Record;
use Equinox\Models\Storage;
use Equinox\Services\General\Utils;
use Illuminate\Support\Collection;

class RecordService
{

    /**
     * Array containing config information
     * @var array
     */
    protected $configInfo;

    /**
     * RecordService constructor.
     */
    public function __construct()
    {
        $this->configInfo = [
            'pivotsConfig' => config('columns.input_output_data.pivot_keys'),
            'hashOutputName' => config('columns.input_output_data.hash_key.output_name'),
            'intervalAggregatesConfig' => array_merge(
                config('columns.input_output_data.interval_column_aggregates'),
                config('columns.input_output_data.interval_column_meta_aggregates')
            ),
        ];
    }


    /**
     * Function used to create
     * @param Storage $storage
     * @return Collection
     */
    public function createRecordValuesFromStorageColumns(Storage $storage): Collection
    {
        $columnNames = $storage->columns->map(function (Column $column) {
            return $column->name;
        });

        return collect(
            array_fill_keys(
                $columnNames->toArray(),
                null
            )
        );
    }

    /**
     * Function used to initialize a new record given a storage
     * @param NamedStorage $namedStorage
     * @param Collection $values
     * @return Record
     */
    public function createEmptyRecord(NamedStorage $namedStorage, Collection $values): Record
    {
        $record = new Record($namedStorage->getStorageName(), $values);

        return $record;
    }

    /**
     * Function used to fill a record with values given the storage record and input data
     * @param NamedStorage $storage
     * @param Record $outputRecord
     * @param array $inputRecord
     * @param string $operation
     */
    public function fillRecord(NamedStorage $storage, Record $outputRecord, array $inputRecord, string $operation)
    {
        $this->computeHashAndPivotValues($outputRecord, $inputRecord)
            ->computeIntervalValues($outputRecord, $inputRecord, $operation, $storage);
    }

    /**
     * Function used to compute the hash and pivot values
     * @param Record $outputRecord
     * @param array $inputRecord
     * @return RecordService
     */
    protected function computeHashAndPivotValues(Record $outputRecord, array $inputRecord): self
    {
        $hashValues = [];

        foreach ($this->configInfo['pivotsConfig'] as $pivotConfig) {
            $columnName = $pivotConfig['output_name'];
            $columnValue = $inputRecord[$pivotConfig['input_name']];

            $outputRecord->setValueForColumn(
                $columnName,
                $columnValue
            );

            $hashValues[$columnName] = $columnValue;
        }

        ksort($hashValues);

        $hash = Utils::hashFromArray($hashValues);

        $outputRecord->setValueForColumn($this->configInfo['hashOutputName'], $hash);
        $outputRecord->hash = $hash;

        return $this;
    }

    /**
     * Function used to compute the interval column values
     * @param Record $outputRecord
     * @param array $inputRecord
     * @param string $operation
     * @param NamedStorage $storage
     * @return RecordService
     */
    protected function computeIntervalValues(
        Record $outputRecord,
        array $inputRecord,
        string $operation,
        NamedStorage $storage
    ): self {
        $intervalColumnName = $storage->getIntervalColumnNameByReferenceDate();

        $values = collect([]);

        foreach ($this->configInfo['intervalAggregatesConfig'] as $config) {
            $value = $this->extractValueFromInputRecord($inputRecord, $config, $operation);
            $aggregateKey = $config['aggregate_key'];

            $values->put($aggregateKey, $value);
        }

        $outputRecord->setValueForColumn($intervalColumnName, $values->toJson());

        return $this;
    }

    /**
     * Function used to extract the value from given record
     * @param array $record
     * @param array $aggregateConfig
     * @param string $operation
     * @return float|int|null
     * @throws ColumnException
     */
    protected function extractValueFromInputRecord(array $record, array $aggregateConfig, string $operation)
    {
        $inputKey = $aggregateConfig['input_name'];
        $inputFunction = $aggregateConfig['input_function'];

        $sign = $this->getSignByOperation($operation);

        switch ($inputFunction) {
            case 'sum':
                /* Return 0 if value does not exist in given record */
                if (!array_key_exists($inputKey, $record)) {
                    return null;
                }

                /* Otherwise return the value from the record */

                return $sign * floatval($record[$inputKey]);
            case 'count':
                /* Always return one unit if input_name is null */
                if (is_null($inputKey)) {
                    return $sign * 1;
                }

                /* Otherwise evaluate column and check if value is considered non-zero */

                return $sign * intval(boolval($record[$inputKey]));
            default:
                throw new ColumnException(
                    ColumnException::INVALID_CONFIG_FUNCTION_RECEIVED,
                    $aggregateConfig
                );
        }
    }

    /**
     * Short function used to return value sign by given operation
     * @param string $operation
     * @return int
     * @throws DataException
     */
    protected function getSignByOperation(string $operation): int
    {
        switch ($operation) {
            case Data::INSERT_OPERATION:
                return 1;
            case Data::DELETE_OPERATION:
                return -1;
            default:
                throw new DataException(DataException::INVALID_OPERATION_RECEIVED, [
                    'Operation' => $operation,
                ]);
        }
    }
}