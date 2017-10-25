<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 08/09/17
 * Time: 15:49
 */

return [

    /**
     * The interval stored in the JSON column of a reporting table (in minutes).
     * Allowed options are stored in App\Definitions\Common.php
     */
    'data_elasticity' => 60,


    /**
     * The interval the reporting table should store data.
     * Allowed options are stored in App\Definitions\Common.php
     */
    'storage_elasticity' => 'daily',


    /**
     * Flag used to detect whether parallel processing for certain features is enabled such as data fetching
     */
    'gearman_parallel_processing' => false,


    /**
     * Flag used to determine if data operations (insert / delete / update) are instantly processed
     * or should be queued and executed at certain intervals.
     * Allowed options are any integer value between 1 (1 minute) and 1440 (24 hours) or false.
     * Numeric values represent the interval between each batch processing in minutes.
     */
    'batch_processing' => 1000,

    /**
     * How many records to modify at a time
     */
    'batch_database_records' => 500
];