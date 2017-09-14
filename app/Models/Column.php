<?php

namespace Equinox\Models;


use Equinox\Exceptions\ColumnException;
use Equinox\Exceptions\ModelException;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class Column
 * @package Equinox\Models
 * @property string $name
 * @property string $data_type
 * @property int|null $length
 * @property string|null $index
 * @property bool $allow_null
 * @property array $extra
 */
abstract class Column implements Arrayable
{
    /**
     * Column meta fields
     */
    protected const NAME = 'name';
    protected const DATA_TYPE = 'data_type';
    protected const LENGTH = 'length';
    protected const INDEX = 'index';
    protected const ALLOW_NULL = 'allow_null';
    protected const EXTRA = 'extra';

    /**
     * An array containing column meta information
     * @var array
     */
    protected $meta;

    /**
     * The column value
     * @var ColumnValue
     */
    protected $value;

    /**
     * Array used to decide which class properties can be fetched
     * @var array
     */
    protected $gettable = [
        self::NAME,
        self::DATA_TYPE,
        self::LENGTH,
        self::INDEX,
        self::ALLOW_NULL,
        self::EXTRA,
    ];

    /**
     * Column constructor.
     * @param array $meta
     */
    public function __construct(array $meta)
    {
        $this->validateMetaColumnInformation($meta)
            ->applyDefaultsAndSave($meta);
    }

    /**
     * Function used to apply default values to meta information and store it
     * @param array $meta
     * @return Column
     */
    protected function applyDefaultsAndSave(array $meta): self
    {
        $defaults = $this->getDefaultValues();

        $meta = array_merge($meta, $defaults);

        $this->meta = $meta;

        return $this;
    }

    /**
     * Function used to return the default values that should be merged with current ones
     * @return array
     */
    abstract protected function getDefaultValues(): array;

    /**
     * Function used to validate
     * @param array $meta
     * @return Column
     * @throws ColumnException
     */
    protected function validateMetaColumnInformation(array $meta): self
    {
        $rules = $this->getValidationRules();

        $validator = \Validator::make($meta, $rules);

        if ($validator->fails()) {
            throw new ColumnException(ColumnException::VALIDATION_FAILED, $validator->errors());
        }

        return $this;
    }

    /**
     * Function used to return the validation rules for column information
     * @return array
     */
    abstract protected function getValidationRules(): array;

    /**
     * Short function used to set the column value
     * @param ColumnValue $value
     * @return Column
     */
    public function setColumnValue(ColumnValue $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Magic getter for meta information
     * @param string $property
     * @return mixed
     * @throws ModelException
     */
    public function __get(string $property)
    {
        /* Throw error if property not allowed to be fetched */
        if (!in_array($property, $this->gettable)) {
            throw new ModelException(ModelException::PROPERTY_NOT_GETTABLE, [
                'property' => $property,
            ]);
        }

        if ($property == self::EXTRA) {
            return $this->computeExtra();
        }

        return $this->meta[$property];
    }

    /**
     * Short function used to compute the extra key for a column
     * @return array
     */
    protected function computeExtra(): array
    {
        if (
            !array_key_exists(self::LENGTH, $this->meta) ||
            is_null($this->meta[self::LENGTH])
        ) {
            return [];
        }

        return array_intersect_key(
            $this->meta,
            array_flip([
                self::LENGTH,
            ])
        );
    }


    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'type' => $this->getColumnType(),
            'meta' => $this->getColumnMeta(),
            //'value' => $this->getColumnValue()
        ];
    }

    /**
     * Short function used to get the column type
     * @return string
     */
    abstract public function getColumnType(): string;

    /**
     * Short function used to get all column meta information
     * @return array
     */
    public function getColumnMeta(): array
    {
        return $this->meta;
    }

    /**
     * Short function used to get the column value
     * @return ColumnValue
     */
    public function getColumnValue(): ColumnValue
    {
        //TODO: add ?? SimpleValue(null)
        return $this->value;
    }
}
