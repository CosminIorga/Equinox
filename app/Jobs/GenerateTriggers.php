<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 11/09/17
 * Time: 16:30
 */

namespace Equinox\Jobs;


use Equinox\Models\Storage;
use Illuminate\Support\Collection;

class GenerateTriggers extends DefaultJob
{
    /**
     * Storage triggers
     * @var Collection
     */
    protected $triggers;

    /**
     * The associated storage whose columns will be computed
     * @var Storage
     */
    protected $storage;

    /**
     * GenerateTriggers constructor.
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
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



        return $this;
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
     * @param string $trigger
     * @return GenerateTriggers
     */
    protected function storeTrigger(string $trigger): self
    {
        $this->triggers->push($trigger);

        return $this;
    }
}