<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 20/09/17
 * Time: 14:50
 */

namespace Equinox\Services\Structure;


use Equinox\Helpers\IntervalColumnHelper;
use Equinox\Models\Column;
use Equinox\Models\NamedStorage;
use Equinox\Models\Record;
use Equinox\Models\Storage;
use Equinox\Services\General\Utils;
use Illuminate\Support\Collection;

class RecordService
{

    use IntervalColumnHelper;

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
     */
    public function fillRecord(NamedStorage $storage, Record $outputRecord, array $inputRecord)
    {
        $this->computeHashAndPivotValues($outputRecord, $inputRecord)
            ->computeIntervalValues($storage, $outputRecord, $inputRecord);
    }

    /**
     * Function used to compute the hash and pivot values
     * @param Record $outputRecord
     * @param array $inputRecord
     * @return RecordService
     */
    protected function computeHashAndPivotValues(Record $outputRecord, array $inputRecord): self
    {
        $pivotsConfig = config('columns.input_output_data.pivot_keys');
        $hashOutputName = config('columns.input_output_data.hash_key.output_name');

        $hashValues = [];

        foreach ($pivotsConfig as $pivotConfig) {
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

        $outputRecord->setValueForColumn($hashOutputName, $hash);

        return $this;
    }

    /**
     * Function used to compute the interval column values
     * @param NamedStorage $storage
     * @param Record $outputRecord
     * @param array $inputRecord
     * @return RecordService
     */
    protected function computeIntervalValues(NamedStorage $storage, Record $outputRecord, array $inputRecord): self
    {
        $intervalAggregatesConfig = $this->getAggregatesConfig();

        $intervalColumnName = $storage->getIntervalColumnNameByReferenceDate();

        $values = collect([]);

        foreach ($intervalAggregatesConfig as $config) {
            $value = $this->extractValueFromInputRecord($inputRecord, $config);
            $aggregateKey = $config['aggregate_key'];

            $values->put($aggregateKey, $value);
        }

        $outputRecord->setValueForColumn($intervalColumnName, $values->toJson());

        return $this;
    }
}