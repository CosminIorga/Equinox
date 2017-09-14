<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 11:34
 */

namespace Equinox\Models\ColumnTypes;


use Equinox\Definitions\Columns;
use Equinox\Models\Column;

class HashColumn extends Column
{


    /**
     * Function used to return the default values that should be merged with current ones
     * @return array
     */
    protected function getDefaultValues(): array
    {
        return [
            self::DATA_TYPE => Columns::STRING_DATA_TYPE,
            self::LENGTH => 32,
            self::INDEX => Columns::PRIMARY_INDEX,
            self::ALLOW_NULL => false,
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
        return Columns::HASH_COLUMN;
    }
}