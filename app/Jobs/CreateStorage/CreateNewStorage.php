<?php

namespace Equinox\Jobs\CreateStorage;


use Carbon\Carbon;
use Equinox\Jobs\DefaultJob;
use Equinox\Models\NamedStorage;
use Equinox\Services\Repositories\DataService;
use Equinox\Services\Structure\StorageService;

/**
 * Class CreateNewStorage
 * @package Equinox\Jobs
 */
class CreateNewStorage extends DefaultJob
{
    /**
     * The reference date
     * @var Carbon
     */
    protected $referenceDate;

    /**
     * The storage type
     * @var string
     */
    protected $storageType;

    /**
     * The named storage model
     * @var NamedStorage
     */
    protected $namedStorage;

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
     * CreateNewStorage constructor.
     * @param Carbon $referenceDate
     * @param string $storageType
     */
    public function __construct(Carbon $referenceDate, string $storageType)
    {
        $this->referenceDate = $referenceDate;
        $this->storageType = $storageType;
    }

    /**
     * Job runner
     * @param DataService $dataService
     * @param StorageService $storageService
     */
    public function handle(
        DataService $dataService,
        StorageService $storageService
    ) {
        $this->dataService = $dataService;
        $this->storageService = $storageService;

        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        $this->initStorage()
            ->generateStorageTriggers()
            ->persist();

        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

        echo "CREATED: $elapsed" . PHP_EOL;
    }

    /**
     * Short function used to set various storage fields
     * @return CreateNewStorage
     */
    protected function initStorage(): self
    {
        $storage = $this->storageService->createStorageByType($this->storageType);

        $this->namedStorage = $this->storageService->createNamedStorage($storage, $this->referenceDate);

        return $this;
    }

    /**
     * Short function used to generate the storage triggers
     * @return CreateNewStorage
     */
    protected function generateStorageTriggers(): self
    {
        $this->storageService->computeStorageTriggers($this->namedStorage);

        return $this;
    }

    /**
     * Function used to save the Storage Model
     * @return CreateNewStorage
     */
    protected function persist(): self
    {
        $this->dataService->generateStorage($this->namedStorage);

        return $this;
    }
}
