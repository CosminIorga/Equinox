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
use Equinox\Models\ColumnValue;
use Equinox\Models\ColumnValueTypes\SimpleValue;
use Illuminate\Validation\Rule;

class PivotColumn extends Column
{

    /**
     * Function used to return the default values that should be merged with current ones
     * @return array
     */
    protected function getDefaultValues(): array
    {
        return [
            self::INDEX => Columns::SIMPLE_INDEX,
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
            self::DATA_TYPE => Rule::in(Columns::DATA_TYPES),
            self::LENGTH => 'nullable|integer',
        ];
    }

    /**
     * Short function used to get the column type
     * @return string
     */
    public function getColumnType(): string
    {
        return Columns::PIVOT_COLUMN;
    }
}