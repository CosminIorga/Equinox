<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 20/09/17
 * Time: 17:18
 */

namespace Equinox\Models\ColumnValueTypes;


use Equinox\Models\ColumnValue;
use Illuminate\Support\Collection;

class AggregateValue extends ColumnValue
{

    /**
     * AggregateValue constructor.
     * @param Collection $simpleValues
     */
    public function __construct(Collection $simpleValues)
    {
        $this->setValue($simpleValues);
    }

    /**
     * Function used to return the values as JSON
     * @return string
     */
    public function getValue()
    {
        /** @var Collection $values */
        $values = $this->_value;

        $values = $values->map(function (SimpleValue $value) {
            return $value->getValue();
        });


        return $values->toJson();
    }
}