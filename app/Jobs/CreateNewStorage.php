<?php

namespace Equinox\Jobs;


use Carbon\Carbon;
use Equinox\Models\Storage;
use Equinox\Models\StorageOptions;
use Equinox\Services\General\StorageService;
use Equinox\Services\Repositories\DataService;

class CreateNewStorage extends DefaultJob
{
    /**
     * The reference date
     * @var Carbon
     */
    protected $referenceDate;

    /**
     * The storage options
     * @var StorageOptions
     */
    protected $storageOptions;

    /**
     * The storage model
     * @var Storage
     */
    protected $storage;

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
     */
    public function __construct(Carbon $referenceDate)
    {
        $this->referenceDate = $referenceDate;
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

        $this->computeStorageOptions()
            ->computeStorage()
            ->persist();
    }

    /**
     * Function used to compute the storage options used to build the storage
     * @return CreateNewStorage
     */
    protected function computeStorageOptions(): self
    {
        $this->storageOptions = new StorageOptions(
            config('general.storage_elasticity'),
            true,
            true
        );

        return $this;
    }

    /**
     * Function used to initialize a Storage Model
     * @return CreateNewStorage
     */
    protected function computeStorage(): self
    {
        $this->storage = $this->storageService->computeNewStorage(
            $this->referenceDate,
            $this->storageOptions
        );

        return $this;
    }


    /**
     * Function used to save the storage model
     * @return CreateNewStorage
     */
    protected function persist(): self
    {
        $this->dataService->generateStorage($this->storage);

        return $this;
    }
}
