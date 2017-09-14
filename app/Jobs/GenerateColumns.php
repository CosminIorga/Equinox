<?php

namespace Equinox\Jobs;


use Equinox\Definitions\Columns;
use Equinox\Factories\ColumnFactory;
use Equinox\Models\Column;
use Equinox\Models\Storage;
use Illuminate\Support\Collection;

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
        $intervalPatternName = config('columns.storage_columns.interval_column_template.name_pattern');

        $intervalColumnCount = $this->storage->getIntervalColumnCount();

        foreach (range(1, $intervalColumnCount) as $columnIndex) {
            $intervalColumnName = $this->replaceInPattern(
                $intervalPatternName,
                $this->storage->getValueForCoordinate($columnIndex - 1),
                $this->storage->getValueForCoordinate($columnIndex)
            );

            $intervalColumn = ColumnFactory::build([
                'name' => $intervalColumnName,
            ], Columns::INTERVAL_COLUMN);

            $this->storeColumn($intervalColumn);
        }

        return $this;
    }

    /**
     * Function used to replace the two coordinates in intervalPattern
     * @param string $pattern
     * @param string $replace1
     * @param string $replace2
     * @return string
     */
    protected function replaceInPattern(string $pattern, string $replace1, string $replace2): string
    {
        return str_replace(
            [
                ':start_interval:',
                ':end_interval:',
            ],
            [
                $replace1,
                $replace2,
            ],
            $pattern
        );
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
