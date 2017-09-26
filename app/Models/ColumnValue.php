<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 12:03
 */

namespace Equinox\Models;


abstract class ColumnValue
{
    /**
     * The stored column value
     * @var mixed
     */
    protected $_value;

    /**
     * Set the column value
     * @param $value
     */
    protected function setValue($value)
    {
        $this->_value = $value;
    }

    /**
     * Return the column value
     * @return mixed
     */
    public function getValue()
    {
        return $this->_value;
    }

}