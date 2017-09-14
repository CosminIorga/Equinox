<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 14/09/17
 * Time: 15:33
 */

namespace Equinox\Services\General;


use Carbon\Carbon;
use Equinox\Factories\StorageFactory;
use Equinox\Jobs\GenerateColumns;
use Equinox\Jobs\GenerateTriggers;
use Equinox\Models\Storage;
use Equinox\Models\StorageOptions;

class StorageService
{

    /**
     * @var Carbon
     */
    protected $referenceDate;

    /**
     * The storage options used to initialize the storage
     * @var StorageOptions
     */
    protected $storageOptions;

    /**
     * The storage model
     * @var Storage
     */
    protected $storage;

    /**
     * Return a Storage model based on reference date and storage options
     * @param Carbon $referenceDate
     * @param StorageOptions $storageOptions
     * @return Storage
     */
    public function computeNewStorage(Carbon $referenceDate, StorageOptions $storageOptions): Storage
    {
        $this->referenceDate = $referenceDate;
        $this->storageOptions = $storageOptions;

        $this->initStorage()
            ->computeStorageColumns()
            ->computeStorageTriggers();

        return $this->storage;
    }

    /**
     * Function used to create a new empty storage based on reference date
     * @return $this
     */
    protected function initStorage()
    {
        $type = $this->storageOptions->storageType;

        $this->storage = StorageFactory::build($this->referenceDate, $type);

        return $this;
    }

    /**
     * Function used to compute storage columns given the storage model
     * @return StorageService
     */
    protected function computeStorageColumns(): self
    {
        /* Do not compute columns if not requested */
        if (!$this->storageOptions->columnsFlag) {
            return $this;
        }

        $columnsGenerator = new GenerateColumns($this->storage);
        $this->storage->columns = dispatch_now($columnsGenerator);

        return $this;
    }

    /**
     * Function used to compute storage triggers given the storage model
     * @return StorageService
     */
    protected function computeStorageTriggers(): self
    {
        /* Do not compute triggers if not requested */
        if (!$this->storageOptions->triggersFlag) {
            return $this;
        }

        $triggersGenerator = new GenerateTriggers($this->storage);
        $this->storage->triggers = dispatch_now($triggersGenerator);

        return $this;
    }
}