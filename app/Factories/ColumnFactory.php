<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 12:40
 */

namespace Equinox\Factories;


use Equinox\Definitions\Columns;
use Equinox\Exceptions\FactoryException;
use Equinox\Models\Column;
use Equinox\Models\ColumnTypes\HashColumn;
use Equinox\Models\ColumnTypes\PivotColumn;
use Equinox\Models\ColumnTypes\IntervalColumn;

class ColumnFactory
{

    /**
     * Column factory builder
     * @param array $config
     * @param string $type
     * @return Column
     * @throws FactoryException
     */
    public static function build(array $config, string $type): Column
    {
        switch($type) {
            case Columns::HASH_COLUMN:
                return new HashColumn($config);
            case Columns::PIVOT_COLUMN:
                return new PivotColumn($config);
            case Columns::INTERVAL_COLUMN:
                return new IntervalColumn($config);
            default:
                throw new FactoryException(FactoryException::INVALID_COLUMN_TYPE_RECEIVED, $type);
        }
    }
}