<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 12:34
 */

namespace Equinox\Models\ColumnTypes;


use Equinox\Definitions\Columns;
use Equinox\Models\Column;

/**
 * Class IntervalColumn
 * @package Equinox\Models\ColumnTypes
 */
class IntervalColumn extends Column
{

    /**
     * Function used to return the default values that should be merged with current ones
     * @return array
     */
    protected function getDefaultValues(): array
    {
        return [
            self::DATA_TYPE => Columns::JSON_DATA_TYPE,
            self::INDEX => Columns::NO_INDEX,
            self::ALLOW_NULL => true,
        ];
    }

    /**
     * Function used to return the validation rules for column information
     * @return array
     */
    protected function getValidationRules(): array
    {
        return [
            self::NAME => 'required|string',
        ];
    }

    /**
     * Short function used to get the column type
     * @return string
     */
    public function getColumnType(): string
    {
        return Columns::INTERVAL_COLUMN;
    }

}