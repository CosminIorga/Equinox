<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 10:48
 */


return [

    /**
     * Here resides all information regarding storage columns
     */
    'storage_columns' => [
        /**
         * Hash column is the primary key of the storage.
         * Some values are hard_coded as of the nature of the column
         * Each hash, pivot and time columns are represented as an array containing:
         *    name => A string representing the column name
         *    data_type => The column type. See available column types in App\Definitions\Columns
         *    length => The length for data type such as varchar(32) or int(11). Default: null
         *    index => See available indexes in App\Definitions\Columns. Default: "index"
         *    allow_null => If the column can be null
         */
        'hash_column' => [
            'name' => 'hash_id',
            #data_type is assumed string
            #length is assumed 32
            #index is assumed primary_key
            #allow_null is assumed false
        ],
        /**
         * Pivot columns are the columns which we will group by
         * All pivot columns will be used in a unique index as to better enforce the reporting algorithm
         */
        'pivot_columns' => [
            'client' => [
                'name' => 'client',
                'data_type' => 'string',
                'length' => 255,
                #index is assumed simple_index
                #allow_null is assumed false
            ],
            'carrier' => [
                'name' => 'carrier',
                'data_type' => 'string',
                'length' => 255,
                #index is assumed simple_index
                #allow_null is assumed false
            ],
            'destination' => [
                'name' => 'destination',
                'data_type' => 'string',
                'length' => 255,
                #index is assumed simple_index
                #allow_null is assumed false
            ],
        ],

        /**
         * Interval column contains only template values as interval column count depends on the storage config
         */
        'interval_column_template' => [
            #name_pattern is assumed 'interval_:start_interval:_:end_interval:'
            #data_type is assumed JSON
            #index is assumed null
            #allow_null is assumed true
        ],

    ],

    /**
     * Here resides information regarding the raw input data that needs to be inserted
     */
    'input_output_data' => [
        /**
         * Timestamp key is used to compute the storage and interval needed
         */
        'timestamp_key' => [
            'input_name' => 'start_date',
        ],

        /**
         * Pivot keys ... TODO: Explain
         */
        'pivot_keys' => [
            'client' => [
                'input_name' => 'client',
                'output_name' => 'client',
            ],
            'carrier' => [
                'input_name' => 'carrier',
                'output_name' => 'carrier',
            ],
            'destination' => [
                'input_name' => 'destination',
                'output_name' => 'destination',
            ],
        ],

        /**
         * Hash key ... TODO: Explain
         */
        'hash_key' => [
            'output_name' => 'hash_id',
        ],

        /**
         * Interval column aggregates contain information regarding the data that will be stored in interval columns
         * Each aggregate is represented as a key => value pair as follows:
         *      input_name => from which key should it fetch the data
         *      aggregate_key => in which key it should place the parsed data
         *      input_function => function to apply to input_name
         *      output_functions => array containing available operations on the key
         *      extra => output formatting options such as:
         *          round => round output to X decimals
         */
        'interval_column_aggregates' => [
            'interval_duration' => [
                'input_name' => 'duration',
                'aggregate_key' => 'interval_duration',
                'input_function' => 'sum',
                'output_functions' => [
                    'sum',
                    'max',
                    'min',
                ],
                'extra' => [
                    'round' => 4,
                ],
            ],
            'interval_cost' => [
                'input_name' => 'cost',
                'aggregate_key' => 'interval_cost',
                'input_function' => 'sum',
                'output_functions' => [
                    'sum',
                    'max',
                    'min',
                ],
                'extra' => [
                    'round' => 4,
                ],
            ],
            'interval_records' => [
                'input_name' => null,
                'aggregate_key' => 'interval_records',
                'input_function' => 'count',
                'output_functions' => [
                    'sum',
                    'max',
                    'min',
                ],
            ],
            'interval_full_records' => [
                'input_name' => 'is_full_record',
                'aggregate_key' => 'interval_full_records',
                'input_function' => 'count',
                'output_functions' => [
                    'sum',
                ],
            ],
        ],
        /**
         * Meta aggregates contain information used internally by the application
         * Respects same format as a normal aggregate column
         */
        'interval_column_meta_aggregates' => [
            'meta_record_count' => [
                'input_name' => null,
                'aggregate_key' => 'meta_record_count',
                'input_function' => 'count',
                'output_functions' => [
                    'sum',
                ],
            ],
        ],

    ],
];