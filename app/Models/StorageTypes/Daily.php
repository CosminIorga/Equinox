<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 14:13
 */

namespace Equinox\Models\StorageTypes;


use Carbon\Carbon;
use Equinox\Models\Storage;

class Daily extends Storage
{

    /**
     * Function used to retrieve the storage name
     * @return string
     */
    public function getStorageName(): string
    {
        $referenceDate = $this->_referenceDate->format('Y_m_d');
        $className = (new \ReflectionClass($this))->getShortName();

        $tableName = $className . "_" . $referenceDate;

        return $tableName;
    }

    /**
     * Function used to return interval column count
     * @return int
     */
    public function getIntervalColumnCount(): int
    {
        /* Number of minutes in a day divided by number of minutes per interval */
        return 24 * 60 / $this->dataElasticity;
    }

    /**
     * Function used to return a value for given coordinate based on table interval
     * @param int $coordinate
     * @return string
     */
    public function getValueForCoordinate(int $coordinate): string
    {
        $baseDate = $this->getStorageStartDate();

        $baseDate->addMinutes($this->dataElasticity * $coordinate);

        return $baseDate->format('H');
    }

    /**
     * Function used to retrieve the first datetime at which the storage holds information
     * @return Carbon
     */
    public function getStorageStartDate(): Carbon
    {
        return new Carbon($this->_referenceDate->format('Y-m-d'));
    }
}