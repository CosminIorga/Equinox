<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 16:30
 */

namespace Equinox\Jobs;


use Equinox\Definitions\Columns;
use Equinox\Definitions\Storage as StorageDefinitions;
use Equinox\Models\Column;
use Equinox\Models\NamedStorage;
use Equinox\Models\Trigger;
use Illuminate\Support\Collection;

class GenerateTriggers extends DefaultJob
{
    /**
     * The storage triggers
     * @var Collection
     */
    protected $triggers;

    /**
     * The associated storage whose triggers will be computed
     * @var NamedStorage
     */
    protected $namedStorage;

    /**
     * GenerateTriggers constructor.
     * @param NamedStorage $namedStorage
     */
    public function __construct(NamedStorage $namedStorage)
    {
        $this->namedStorage = $namedStorage;
        $this->triggers = collect();
    }

    /**
     * Job runner
     * @return Collection
     */
    public function handle(): Collection
    {
        return $this->computeCleanStorageTrigger()
            ->returnTriggers();
    }

    /**
     * Function used to compute the syntax needed for the "clean storage" trigger
     * @return GenerateTriggers
     */
    protected function computeCleanStorageTrigger(): self
    {
        $trigger = new Trigger(
            $this->namedStorage->getStorageName(),
            $this->computeCleanStorageTriggerName(),
            $this->computeCleanStorageTriggerType(),
            $this->computeCleanStorageTriggerDefinition()
        );

        $this->storeTrigger($trigger);

        return $this;
    }

    /**
     * Function used to return the name of the trigger
     * @return string
     */
    protected function computeCleanStorageTriggerName(): string
    {
        return "clean_storage_" . $this->namedStorage->getStorageName();
    }

    /**
     * Function used to return the trigger type
     * @return string
     */
    protected function computeCleanStorageTriggerType(): string
    {
        return "BEFORE UPDATE";
    }

    /**
     * Function used to return the trigger definition
     * @return string
     */
    protected function computeCleanStorageTriggerDefinition(): string
    {
        /* Get the interval columns */
        $intervalColumns = $this->namedStorage->columns->filter(function (Column $column) {
            return $column->getColumnType() == Columns::INTERVAL_COLUMN;
        });

        /* Compute trigger logic for each interval column */
        $columnLogic = $intervalColumns->map(function (Column $column) {
            return sprintf(
                StorageDefinitions::CHECK_COLUMN_IS_NULL_TEMPLATE,
                $column->name
            );
        });

        /* Compute trigger logic for each interval row */
        $rowLogic = sprintf(
            StorageDefinitions::CHECK_ROW_IS_NULL_TEMPLATE,
            $intervalColumns->map(function (Column $column) {
                return "NEW." . $column->name;
            })->implode(', ')
        );

        $storageDefinition = $columnLogic->implode(PHP_EOL) . PHP_EOL . $rowLogic;

        return $storageDefinition;
    }

    /**
     * Small function used to return computed triggers
     * @return Collection
     */
    protected function returnTriggers(): Collection
    {
        return $this->triggers;
    }

    /**
     * Small function used to store a column
     * @param Trigger $trigger
     * @return GenerateTriggers
     */
    protected function storeTrigger(Trigger $trigger): self
    {
        $this->triggers->push($trigger);

        return $this;
    }
}