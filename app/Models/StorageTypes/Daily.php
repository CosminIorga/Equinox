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

/**
 * Class Daily
 * @package Equinox\Models\StorageTypes
 */
class Daily extends Storage
{

    /**
     * Function used to retrieve the storage name
     * @param Carbon $referenceDate
     * @return string
     */
    public function getStorageName(Carbon $referenceDate): string
    {
        return implode('_', [
            (new \ReflectionClass($this))->getShortName(),
            $referenceDate->format('Y_m_d'),
        ]);
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
     * Function used to return the interval column name given the interval column index
     * @param int $columnIndex
     * @return string
     */
    public function getIntervalColumnNameByIndex(int $columnIndex): string
    {
        $intervalPatternName = self::INTERVAL_COLUMN_PATTERN_NAME;

        return str_replace(
            [
                ':start_interval:',
                ':end_interval:',
            ],
            [
                str_pad($columnIndex + 0, 2, 0, STR_PAD_LEFT),
                str_pad($columnIndex + 1, 2, 0, STR_PAD_LEFT),
            ],
            $intervalPatternName
        );
    }

    /**
     * Function used to return the interval column name by reference date
     * @param Carbon $referenceDate
     * @return string
     */
    public function getIntervalColumnNameByReferenceDate(Carbon $referenceDate): string
    {
        $columnIndex = floor(($referenceDate->secondsSinceMidnight() / 60) / $this->dataElasticity);

        return $this->getIntervalColumnNameByIndex($columnIndex);
    }


    /**
     * Function used to check if given reference date can be processed by storage.
     * Probably all storage(s) except for Dynamic storage, will return true
     * @param Carbon $referenceDate
     * @return bool
     */
    public function storageShouldHandleThisDate(Carbon $referenceDate): bool
    {
        /* This value is indeed the result it should return and it is not hard-coded in any way */
        return true;
    }

}