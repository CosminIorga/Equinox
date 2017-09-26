<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 20/09/17
 * Time: 17:18
 */

namespace Equinox\Models\ColumnValueTypes;


use Equinox\Models\ColumnValue;

class EmptyValue extends ColumnValue
{

    /**
     * EmptyValue constructor.
     */
    public function __construct()
    {
        $this->setValue(null);
    }
}