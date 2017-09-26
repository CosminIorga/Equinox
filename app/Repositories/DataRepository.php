<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 13/09/17
 * Time: 14:39
 */

namespace Equinox\Repositories;


use Illuminate\Database\Query\Builder;

class DataRepository extends DefaultRepository
{


    /**
     * Function used to initialize the query builder
     * @param string $tableName
     * @return Builder
     */
    protected function initQueryBuilder(string $tableName): Builder
    {
        return \DB::table($tableName);
    }


    /**
     * Function used to create a new storage given the storage name and storage generator function
     * @param string $storageName
     * @param \Closure $storageGeneratorClosure
     */
    public function createStorageFromClosure(string $storageName, \Closure $storageGeneratorClosure)
    {
        \Schema::create($storageName, $storageGeneratorClosure);
    }

    /**
     * Function used to create a new storage trigger given the trigger generator function
     * @param \Closure $storageTriggerClosure
     */
    public function createTriggerFromClosure(\Closure $storageTriggerClosure)
    {
        $triggerSyntax = $storageTriggerClosure();

        \DB::unprepared($triggerSyntax);
    }

    /**
     * Function used to drop a storage if exists
     * @param string $storageName
     */
    public function dropStorageIfExists(string $storageName)
    {
        \Schema::dropIfExists($storageName);
    }

}