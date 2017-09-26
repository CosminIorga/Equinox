<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 21/09/17
 * Time: 18:20
 */

namespace Equinox\Helpers;


use Equinox\Exceptions\ColumnException;

trait IntervalColumnHelper
{

    /**
     * Short function used to return the aggregate config
     * @return array
     */
    protected function getAggregatesConfig(): array
    {
        return array_merge(
            config('columns.input_output_data.interval_column_aggregates'),
            config('columns.input_output_data.interval_column_meta_aggregates')
        );
    }

    /**
     * Function used to extract the value from given record
     * @param array $record
     * @param array $aggregateConfig
     * @return null|int|float
     * @throws ColumnException
     */
    protected function extractValueFromInputRecord(array $record, array $aggregateConfig)
    {
        $inputKey = $aggregateConfig['input_name'];
        $inputFunction = $aggregateConfig['input_function'];

        switch ($inputFunction) {
            case 'sum':
                /* Return 0 if value does not exist in given record */
                if (!array_key_exists($inputKey, $record)) {
                    return null;
                }

                /* Otherwise return the value from the record */

                return floatval($record[$inputKey]);
            case 'count':
                /* Always return one unit if input_name is null */
                if (is_null($inputKey)) {
                    return 1;
                }

                /* Otherwise evaluate column and check if value is considered non-zero */

                return intval(boolval($record[$inputKey]));
            default:
                throw new ColumnException(
                    ColumnException::INVALID_CONFIG_FUNCTION_RECEIVED,
                    $aggregateConfig
                );
        }
    }
}