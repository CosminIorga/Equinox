<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 18/09/17
 * Time: 14:49
 */

namespace Equinox\Jobs;


use Equinox\Models\Record;
use Equinox\Models\Storage;
use Equinox\Services\General\Utils;
use Equinox\Services\Repositories\DataService;
use Equinox\Services\Structure\RecordService;
use Equinox\Services\Structure\StorageService;
use Illuminate\Support\Collection;

/**
 * Class ModifyStorageData
 * @package Equinox\Jobs
 */
class ModifyStorageData extends DefaultJob
{

    /**
     * If it should insert or delete the given data
     * @var string
     */
    protected $operation;

    /**
     * The data that needs to be inserted / deleted from the storage
     * @var \Generator
     */
    protected $data;

    /**
     * The data service
     * @var DataService
     */
    protected $dataService;

    /**
     * The storage service
     * @var StorageService
     */
    protected $storageService;

    /**
     * The record service
     * @var RecordService
     */
    protected $recordService;

    /**
     * Collection of records ready to be inserted in / deleted from storage
     * @var Collection
     */
    protected $outputRecords;

    /**
     * ModifyStorageData constructor.
     * @param string $operation
     * @param \Generator $data
     */
    public function __construct(string $operation, \Generator $data)
    {
        $this->operation = $operation;
        $this->data = $data;
        $this->outputRecords = collect([]);
    }

    /**
     * Job runner
     * @param DataService $dataService
     * @param StorageService $storageService
     * @param RecordService $recordService
     */
    public function handle(
        DataService $dataService,
        StorageService $storageService,
        RecordService $recordService
    ) {
        Utils::dumpMemUsage();

        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        $this->dataService = $dataService;
        $this->storageService = $storageService;
        $this->recordService = $recordService;

        /* Get all storage types and iterate over them */
        $storageTypes = $this->getAvailableStorageTypes();

        /* Iterate over storage types and create the records that will be inserted / deleted */
        foreach ($storageTypes as $storageType) {
            /* Get current storage given the storage type */
            $storage = $this->getStorage($storageType);

            /* Add records to outputRecords collection given current storage and input data */
            $this->computeRecordsForStorage($storage);
        }

        /* Group records by storage name */
        $groupedRecords = $this->outputRecords->groupBy(function (Record $record) {
            return $record->storageName;
        });

        /* Modify storage data given the computed records */

        dump($groupedRecords);

        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

        echo "Processed records in $elapsed seconds" . PHP_EOL;

        Utils::dumpMemUsage();
    }

    /**
     * Short function used to retrieve all available storage types
     * @return array
     */
    protected function getAvailableStorageTypes(): array
    {
        //TODO: add logic for Dynamic storage also

        return [
            config('general.storage_elasticity'),
        ];
    }

    /**
     * Function used to retrieve a Storage Model
     * @param string $storageType
     * @return Storage
     */
    protected function getStorage(string $storageType): Storage
    {
        return $this->storageService->createStorageByType($storageType);
    }

    /**
     * Function used to compute a set of Records given current storage and input data
     * @param Storage $storage
     * @return ModifyStorageData
     */
    protected function computeRecordsForStorage(Storage $storage): self
    {
        $defaultValues = $this->recordService->createRecordValuesFromStorageColumns($storage);

        /* Iterate over received data */
        foreach ($this->data as $inputRecord) {
            /* Get timestamp key for input records */
            $timestampKey = config('columns.input_output_data.timestamp_key.input_name');

            $namedStorage = $this->storageService->createNamedStorage($storage, $inputRecord[$timestampKey]);

            /* Check if current storage can handle the input record date */
            if (!$namedStorage->storageShouldHandleCurrentDate()) {
                continue;
            }

            /* Duplicate the default values into a new empty record */
            $outputRecord = $this->recordService->createEmptyRecord($namedStorage, clone $defaultValues);

            /* Fill Storage record with current input record */
            $this->recordService->fillRecord($namedStorage, $outputRecord, $inputRecord);

            /* Add filled record to pool */
            $this->outputRecords->push($outputRecord);
        }

        return $this;
    }
}
