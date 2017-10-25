<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 06/10/17
 * Time: 11:04
 */

namespace Equinox\Jobs\ModifyStorage;


use Equinox\Definitions\Queue;
use Equinox\Jobs\GearmanJob;
use Equinox\Models\QueuePayloadTypes\RQueuePayload;
use Equinox\Models\Record;
use Equinox\Models\Storage;
use Equinox\Services\General\Utils;
use Equinox\Services\Repositories\FileService;
use Equinox\Services\Repositories\QueueService;
use Equinox\Services\Structure\RecordService;
use Equinox\Services\Structure\StorageService;
use Illuminate\Support\Collection;

/**
 * Class ProcessRawRecords
 * @package Equinox\Jobs\ModifyStorage
 */
class ProcessRawRecords extends GearmanJob
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
     * The file service
     * @var FileService
     */
    protected $fileService;

    /**
     * The queue service
     * @var QueueService
     */
    protected $queueService;

    /**
     * Collection of records ready to be inserted in / deleted from storage
     * @var Collection
     */
    protected $outputRecords;


    /**
     * ModifyStorageData constructor.
     * @param string $workerId
     * @param string $operation
     * @param \Generator $data
     */
    public function __construct(string $workerId, string $operation, \Generator $data)
    {
        parent::__construct($workerId);

        $this->operation = $operation;
        $this->data = $data;
        $this->outputRecords = collect([]);
    }

    /**
     * Job runner
     * @param StorageService $storageService
     * @param RecordService $recordService
     * @param FileService $fileService
     * @param QueueService $queueService
     */
    public function handle(
        StorageService $storageService,
        RecordService $recordService,
        FileService $fileService,
        QueueService $queueService
    ) {
        Utils::dumpMemUsage();
        $startTime = Utils::startTimer();

        /* Initialize variables */
        $this->storageService = $storageService;
        $this->recordService = $recordService;
        $this->fileService = $fileService;
        $this->queueService = $queueService;

        /* Get all storage types and iterate over them */
        $storageTypes = $this->getAvailableStorageTypes();

        /* Iterate over storage types and process records */
        foreach ($storageTypes as $storageType) {
            $this->processRecordsForStorageType($storageType);
        }

        Utils::dumpMemUsage();
        $elapsed = Utils::endTimer($startTime);

        Utils::dumpMessage("Processed records in {$elapsed} seconds");
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
     * Function used to process input records given a storage type
     * @param string $storageType
     * @return ProcessRawRecords
     */
    protected function processRecordsForStorageType(string $storageType): self
    {
        /* Get current storage given the storage type */
        $storage = $this->storageService->createStorageByType($storageType);

        $this->processAndWriteOutputRecords($storage);

        return $this;
    }

    /**
     * Function used to compute output records and write them to CSV
     * @param Storage $storage
     * @return ProcessRawRecords
     */
    protected function processAndWriteOutputRecords(Storage $storage): self
    {
        $defaultValues = $this->recordService->createRecordValuesFromStorageColumns($storage);
        $timestampKey = config('columns.input_output_data.timestamp_key.input_name');

        $rQueuePayloads = collect([]);

        foreach ($this->data as $inputRecord) {
            $namedStorage = $this->storageService->createNamedStorage($storage, $inputRecord[$timestampKey]);

            /* Check if current storage can handle the input record date */
            if (!$namedStorage->storageShouldHandleCurrentDate()) {
                continue;
            }

            /* Duplicate the default values into a new empty record */
            $outputRecord = $this->recordService->createEmptyRecord($namedStorage, clone $defaultValues);

            /* Fill Storage record with current input record */
            $this->recordService->fillRecord($namedStorage, $outputRecord, $inputRecord, $this->operation);

            /* Compute CSV file name */
            $fileName = $this->computeFileName($outputRecord);

            /* Write record to pool */
            $this->fileService->writeRecordToCsvFile($fileName, $outputRecord->toCSVData());

            /* Check if current file already has meta information stored */
            if ($rQueuePayloads->has($fileName)) {
                continue;
            }

            /* Otherwise compute and store the meta information */
            $payload = $this->computeRQueuePayload($fileName, $outputRecord);

            $rQueuePayloads->put($fileName, $payload);
        }

        /* Schedule meta info to R-Queue */
        $this->scheduleToQueue($rQueuePayloads);

        return $this;
    }

    /**
     * Short function used to retrieve the CSV File name based on the processed record
     * @param Record $record
     * @return string
     */
    protected function computeFileName(Record $record): string
    {
        return implode("__", [
            $record->storageName,
            $record->hash,
            "WID",
            $this->getWorkerId(),
            $this->getPayloadId(),
        ]);
    }

    /**
     * Function used to retrieve a payload used specifically by the R-Queue
     * @param string $fileName
     * @param Record $outputRecord
     * @return RQueuePayload
     */
    protected function computeRQueuePayload(string $fileName, Record $outputRecord): RQueuePayload
    {
        $payload = new RQueuePayload([
            RQueuePayload::FILE_NAME => $fileName,
            RQueuePayload::NAMED_STORAGE => $outputRecord->storageName,
        ]);

        return $payload;
    }

    /**
     * Schedule payloads to R-Queue
     * @param Collection $payloads
     * @return ProcessRawRecords
     */
    protected function scheduleToQueue(Collection $payloads): self
    {
        /* Open queue connection */
        $this->queueService->openQueueConnection(Queue::R_QUEUE);

        /* Schedule payloads */
        $payloads->each(function (RQueuePayload $payload) {
            $this->queueService->scheduleToQueue(Queue::R_QUEUE, $payload);
        });

        /* Close queue connection */
        $this->queueService->closeQueueConnection(Queue::R_QUEUE);

        return $this;
    }
}