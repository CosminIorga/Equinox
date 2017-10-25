<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 24/10/17
 * Time: 13:06
 */

namespace Equinox\Jobs;

/**
 * Class GearmanJob
 * @package Equinox\Jobs
 */
class GearmanJob extends DefaultJob
{

    /**
     * String used to determine the current payload's unique id
     * @var string
     */
    protected $payloadId;

    /**
     * The worker id assigned by Gearman to execute this job
     * @var string
     */
    protected $workerId;

    /**
     * GearmanJob constructor.
     * @param string $workerId
     */
    public function __construct(string $workerId)
    {
        $this->workerId = $workerId;
        $this->payloadId = uniqid();
    }

    /**
     * Getter for payload id
     * @return string
     */
    public function getPayloadId(): string
    {
        return $this->payloadId;
    }

    /**
     * Getter for worker id
     * @return string
     */
    public function getWorkerId(): string
    {
        return $this->workerId;
    }
}
