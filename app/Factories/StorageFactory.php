<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 12:40
 */

namespace Equinox\Factories;


use Carbon\Carbon;
use Equinox\Definitions\Storage as StorageDefinitions;
use Equinox\Exceptions\FactoryException;
use Equinox\Models\Storage;
use Equinox\Models\StorageTypes\Daily;

class StorageFactory
{

    /**
     * Column factory builder
     * @param Carbon $referenceDate
     * @param string $type
     * @return Storage
     * @throws FactoryException
     */
    public static function build(Carbon $referenceDate, string $type): Storage
    {
        switch($type) {
            case StorageDefinitions::TABLE_ELASTICITY_DAILY:
                return new Daily($referenceDate);
            default:
                throw new FactoryException(FactoryException::INVALID_COLUMN_TYPE_RECEIVED, [
                    'type' => $type
                ]);
        }
    }
}