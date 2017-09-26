<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 20/09/17
 * Time: 14:51
 */

namespace Equinox\Services\Structure;


use Carbon\Carbon;
use Equinox\Factories\StorageFactory;
use Equinox\Jobs\GenerateColumns;
use Equinox\Jobs\GenerateTriggers;
use Equinox\Models\NamedStorage;
use Equinox\Models\Storage;

/**
 * Class StorageService
 * @package Equinox\Services\Structure
 */
class StorageService
{
    /**
     * Return a Storage model based on reference date and storage options
     * @param string $storageType
     * @return Storage
     */
    public function createStorageByType(string $storageType): Storage
    {
        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        /* Create new storage */
        $storage = StorageFactory::build($storageType);

        /* Add storage columns */
        $this->computeStorageColumns($storage);

        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

        echo "Storage generated in {$elapsed} seconds" . PHP_EOL;

        return $storage;
    }

    /**
     * Function used to compute storage columns given the storage model
     * @param Storage $storage
     */
    protected function computeStorageColumns(Storage $storage)
    {
        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        $columnsGenerator = new GenerateColumns($storage);
        $storage->columns = dispatch_now($columnsGenerator);

        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

        echo "Columns generated in {$elapsed} seconds " . PHP_EOL;
    }

    /**
     * Function used to retrieve a named storage given a storage and a reference date
     * @param Storage $storage
     * @param Carbon $referenceDate
     * @return NamedStorage
     */
    public function createNamedStorage(Storage $storage, Carbon $referenceDate): NamedStorage
    {
        return new NamedStorage($storage, $referenceDate);
    }

    /**
     * Function used to compute storage triggers given the storage model
     * @param NamedStorage $storage
     */
    public function computeStorageTriggers(NamedStorage $storage)
    {
        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        $triggersGenerator = new GenerateTriggers($storage);
        $storage->triggers = dispatch_now($triggersGenerator);

        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

        echo "Triggers generated in {$elapsed} seconds " . PHP_EOL;
    }

}