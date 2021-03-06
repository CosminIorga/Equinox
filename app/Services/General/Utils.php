<?php
/**
 * Created by PhpStorm.
 * User: chase
 * Date: 14/08/17
 * Time: 15:52
 */

namespace Equinox\Services\General;


class Utils
{

    /**
     * Small function used to quote a string
     * @param string $string
     * @return string
     */
    static public function quote(string $string): string
    {
        return "'{$string}'";
    }

    /**
     * Short function used escape a string
     * @param string $string
     * @return string
     */
    static public function escapeValue(string $string): string
    {
        return addslashes($string);
    }

    /**
     * Short function used to replace double quotes with single quotes
     * @param string $string
     * @return string
     */
    static public function replaceQuotes(string $string): string
    {
        return str_replace('"', "'", $string);
    }

    /**
     * Shprt function used to return a hash value given an array
     * @param array $values
     * @return string
     */
    static public function hashFromArray(array $values): string
    {
        return md5(implode('__', $values));
    }


    /**
     * Function used to decorate an array in order to make it look similar to MySQL's output
     * @param array $data
     */
    static public function transformToMySQLOutput(array $data)
    {
        $first = true;

        $output = "";

        $spaces = [];

        foreach ($data as $record) {
            foreach ($record as $header => $value) {
                $spaces[$header] = max(strlen($header), strlen($value));
            }
        }

        foreach ($data as $record) {
            /* Show headers if first */
            if ($first) {
                $headers = array_map(function (string $header) use ($spaces) {
                    return str_pad($header, $spaces[$header], " ", STR_PAD_LEFT);
                }, array_keys((array) $record));

                $output .= implode(" | ", $headers) . PHP_EOL;


                $pluses = array_map(function (string $header) use ($spaces) {
                    return str_pad("", $spaces[$header], "-", STR_PAD_BOTH);
                }, array_keys((array) $record));

                $output .= implode('-+-', $pluses);

                $output .= PHP_EOL;

                $first = false;
            }


            $values = array_map(function (string $value, $header) use ($spaces) {
                return str_pad($value, $spaces[$header], " ", STR_PAD_LEFT);
            }, (array) $record, array_keys((array) $record));

            $output .= implode(' | ', $values) . PHP_EOL;
        }

        echo $output;
    }


    /**
     * Small function used to dump the memory usage
     */
    static public function dumpMemUsage()
    {
        /* Do not dump memory usage if not on debug environment */
        if (!env('APP_DEBUG')) {
            return;
        }

        $usage = memory_get_usage();
        $peak = memory_get_peak_usage();

        $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

        foreach (["used" => $usage, "peak" => $peak] as $key => $value) {
            $result = @round($value / pow(1024, ($i = (int) floor(log($value, 1024)))), 2) . ' ' . $unit[$i];

            dump("{$key} = {$result}");
        }

        dump(str_repeat("#", 15));
    }

    /**
     * Start timer for performance benchmarks
     * @return float
     */
    static public function startTimer(): float
    {
        return microtime(true);
    }

    /**
     * Compute total operations time
     * @param float $startTime
     * @return float
     */
    static public function endTimer(float $startTime): float
    {
        return microtime(true) - $startTime;
    }

    /**
     * Dump debug message
     * @param string $message
     */
    static public function dumpMessage(string $message)
    {
        /* Do not dump message if not on debug environment */
        if (!env('APP_DEBUG')) {
            return;
        }

        dump($message);
    }
}