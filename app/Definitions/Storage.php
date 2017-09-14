<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 14:49
 */

namespace Equinox\Definitions;


class Storage
{

    /**
     * Data elasticities
     */
    const DATA_ELASTICITY_15_MINUTES = 15;
    const DATA_ELASTICITY_30_MINUTES = 30;
    const DATA_ELASTICITY_60_MINUTES = 60;
    const DATA_ELASTICITY_120_MINUTES = 120;

    const DATA_ELASTICITIES = [
        self::DATA_ELASTICITY_15_MINUTES,
        self::DATA_ELASTICITY_30_MINUTES,
        self::DATA_ELASTICITY_60_MINUTES,
        self::DATA_ELASTICITY_120_MINUTES
    ];

    /**
     * Table elasticities
     */
    const TABLE_ELASTICITY_QUARTER_DAY = 'quarter';
    const TABLE_ELASTICITY_HALF_DAY = 'half';
    const TABLE_ELASTICITY_DAILY = 'daily';

    const TABLE_ELASTICITIES = [
        self::TABLE_ELASTICITY_QUARTER_DAY,
        self::TABLE_ELASTICITY_HALF_DAY,
        self::TABLE_ELASTICITY_DAILY
    ];
}