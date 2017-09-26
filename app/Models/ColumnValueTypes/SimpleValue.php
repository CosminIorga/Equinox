<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 20/09/17
 * Time: 17:18
 */

namespace Equinox\Models\ColumnValueTypes;


use Equinox\Models\ColumnValue;

class SimpleValue extends ColumnValue
{

    /**
     * SimpleValue constructor.
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->setValue($value);
    }
}