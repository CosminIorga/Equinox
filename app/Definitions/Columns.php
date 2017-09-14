<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 12:14
 */

namespace Equinox\Definitions;


class Columns
{

    /**
     * Available column types
     */
    const HASH_COLUMN = 'primary';
    const PIVOT_COLUMN = 'pivot';
    const TIME_COLUMN = 'time';
    const INTERVAL_COLUMN = 'interval';

    const COLUMN_TYPES = [
        self::HASH_COLUMN,
        self::PIVOT_COLUMN,
        self::TIME_COLUMN,
        self::INTERVAL_COLUMN,
    ];

    /**
     * Available column data types
     */
    const STRING_DATA_TYPE = 'string';
    const INT_DATA_TYPE = 'integer';
    const JSON_DATA_TYPE = 'json';
    const DATETIME_DATA_TYPE = 'datetime';
    const TIMESTAMP_DATA_TYPE = 'timestamp';

    const DATA_TYPES = [
        self::INT_DATA_TYPE,
        self::STRING_DATA_TYPE,
        self::JSON_DATA_TYPE,
        self::DATETIME_DATA_TYPE,
        self::TIMESTAMP_DATA_TYPE,
    ];

    /**
     * Available column indexes
     */
    const PRIMARY_INDEX = 'primary';
    const UNIQUE_INDEX = 'unique';
    const SIMPLE_INDEX = 'index';
    const NO_INDEX = null;

    const INDEXES = [
        Columns::SIMPLE_INDEX,
        Columns::UNIQUE_INDEX,
        Columns::PRIMARY_INDEX,
        Columns::NO_INDEX,
    ];
}