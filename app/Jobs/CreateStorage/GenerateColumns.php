<?php

namespace Equinox\Jobs\CreateStorage;


use Equinox\Definitions\Columns;
use Equinox\Factories\ColumnFactory;
use Equinox\Jobs\DefaultJob;
use Equinox\Models\Column;
use Equinox\Models\Storage;
use Illuminate\Support\Collection;

/**
 * Class GenerateColumns
 * @package Equinox\Jobs
 */
class GenerateColumns extends DefaultJob
{
    /**
     * A collection of Columns
     * @var \Illuminate\Support\Collection
     */
    protected $columns;

    /**
     * The associated storage whose columns will be computed
     * @var Storage
     */
    protected $storage;

    /**
     * GenerateColumns constructor.
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
        $this->columns = collect();
    }

    /**
     * Job runner
     * @return Collection
     */
    public function handle(): Collection
    {
        return $this->computeHashColumn()
            ->computePivotColumns()
            ->computeIntervalColumns()
            ->returnTableStructure();
    }

    /**
     * Function used to compute hash column
     * @return GenerateColumns
     */
    protected function computeHashColumn(): self
    {
        $hashConfig = config('columns.storage_columns.hash_column');
        $hashColumn = ColumnFactory::build($hashConfig, Columns::HASH_COLUMN);

        $this->storeColumn($hashColumn);

        return $this;
    }

    /**
     * Function used to compute pivot columns
     * @return GenerateColumns
     */
    protected function computePivotColumns(): self
    {
        $pivotsConfig = config('columns.storage_columns.pivot_columns');

        foreach ($pivotsConfig as $pivotConfig) {
            $pivotColumn = ColumnFactory::build($pivotConfig, Columns::PIVOT_COLUMN);

            $this->storeColumn($pivotColumn);
        }

        return $this;
    }

    /**
     * Function used to compute interval columns
     * @return GenerateColumns
     */
    protected function computeIntervalColumns(): self
    {
        $intervalColumnCount = $this->storage->getIntervalColumnCount();

        foreach (range(0, $intervalColumnCount - 1) as $columnIndex) {
            $intervalColumn = ColumnFactory::build([
                'name' => $this->storage->getIntervalColumnNameByIndex($columnIndex),
            ], Columns::INTERVAL_COLUMN);

            $this->storeColumn($intervalColumn);
        }

        return $this;
    }

    /**
     * Short function used to return the table columns
     * @return Collection
     */
    protected function returnTableStructure(): Collection
    {
        return $this->columns;
    }

    /**
     * Small function used to store a column
     * @param Column $column
     * @return GenerateColumns
     */
    protected function storeColumn(Column $column): self
    {
        $this->columns->push($column);

        return $this;
    }
}
