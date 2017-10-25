<?php

namespace Equinox\Console\Commands;

use Carbon\Carbon;
use Equinox\Jobs\CreateStorage\CreateNewStorage;
use Equinox\Jobs\ModifyStorage\ProcessRawRecords;
use Equinox\Services\General\Utils;
use Equinox\Services\Repositories\FileService;
use Illuminate\Console\Command;

class Simulate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulate:job {job}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test specific job to see it actually works';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $function = $this->argument('job');

        switch ($function) {
            case "create":
                $this->simulateCreating();
                break;
            case "modify":
                $this->simulateModifying();
                break;
            case "fetch":

                break;
            case "insert":
                $this->testInsertion();
                break;
            case "raw":
                $this->testRaw();
                break;
            case "csv":
                $this->testCsv();
                break;
            default:
                dump("I did nothing");
                break;
        }
    }

    protected function simulateCreating()
    {
        $referenceDate = new Carbon();
        $storageType = config('general.storage_elasticity');

        $job = new CreateNewStorage($referenceDate, $storageType);

        dispatch_now($job);
    }

    protected function simulateModifying()
    {
        $generator = function () {
            $range = 5000;

            $clients = range("A", "B");
            $destinations = range(1, 1);
            $referenceDate = "2017-10-10";


            foreach (range(1, $range) as $index) {
                yield [
                    'is_full_record' => (bool) rand(0, 1),
                    'client' => "company_" . $clients[rand(0, count($clients) - 1)],
                    'carrier' => "company_" . $clients[rand(0, count($clients) - 1)],
                    'destination' => "destination_" . $destinations[rand(0, count($destinations) - 1)],
                    'start_date' => (new Carbon($referenceDate))
                        ->subDays(rand(0, 0))
                        ->setTime(rand(0, 24), rand(0, 24), 0),
                    'duration' => rand(0, 100),
                    'cost' => rand(100, 240) / 100,
                ];
            }
        };


        $job = new ProcessRawRecords(7, 'delete', $generator());

        dispatch_now($job);

    }

    protected function testInsertion()
    {
        $generator = function () {
            $range = 10000;

            $clients = range("A", "B");
            $destinations = range(1, 1);
            $referenceDate = "2017-05-15";


            foreach (range(1, $range) as $index) {
                $record = [
                    'hash_id' => $index,
                    'client' => "company_" . $clients[rand(0, count($clients) - 1)],
                    'carrier' => "company_" . $clients[rand(0, count($clients) - 1)],
                    'destination' => "destination_" . $destinations[rand(0, count($destinations) - 1)],
                    'interval_00_01' => null,
                    'interval_01_02' => null,
                    'interval_02_03' => null,
                    'interval_03_04' => null,
                    'interval_04_05' => null,
                    'interval_05_06' => null,
                    'interval_06_07' => null,
                    'interval_07_08' => null,
                    'interval_08_09' => null,
                    'interval_09_10' => null,
                    'interval_10_11' => null,
                    'interval_11_12' => null,
                    'interval_12_13' => null,
                    'interval_13_14' => null,
                    'interval_14_15' => null,
                    'interval_15_16' => null,
                    'interval_16_17' => null,
                    'interval_17_18' => null,
                    'interval_18_19' => null,
                    'interval_19_20' => null,
                    'interval_20_21' => null,
                    'interval_21_22' => null,
                    'interval_22_23' => null,
                    'interval_23_24' => null,
                ];

                $coordinate = (new Carbon($referenceDate))
                    ->setTime(rand(0, 24), rand(0, 24), 0)
                    ->format('H');

                $intervalValue = json_encode([
                    'interval_duration' => rand(0, 100),
                    'interval_cost' => rand(100, 240) / 100,
                    'interval_records' => 1,
                    'interval_full_records' => rand(0, 1),
                    'meta_record_count' => 1,
                ]);

                $columnName = "interval_" .
                    str_pad($coordinate, 2, 0, STR_PAD_LEFT) .
                    "_" .
                    str_pad(++$coordinate, 2, 0, STR_PAD_LEFT);


                $record[$columnName] = $intervalValue;

                yield $record;
            }
        };

        $data = [];
        foreach ($generator() as $record) {
            $data[] = $record;
        }

        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        \DB::table('test_insertion')
            ->insert($data);


        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

        echo "Elapsed $elapsed" . PHP_EOL;
    }

    protected function testRaw()
    {
        /* Start timer for performance benchmarks */
        $startTime = microtime(true);

        $csv = 'data.csv';
        $query = sprintf(
            "LOAD DATA local INFILE '%s' 
            INTO TABLE test_memory 
            FIELDS TERMINATED BY ',' OPTIONALLY ENCLOSED BY '\"' 
            ESCAPED BY '\"' 
            LINES TERMINATED BY '\\n' 
            IGNORE 0 LINES 
            (
                hash_id, 
                client, 
                carrier, 
                destination, 
                interval_00_01,
                interval_01_02,
                interval_02_03,
                interval_03_04,
                interval_04_05,
                interval_05_06,
                interval_06_07,
                interval_07_08,
                interval_08_09,
                interval_09_10,
                interval_10_11,
                interval_11_12,
                interval_12_13,
                interval_13_14,
                interval_14_15,
                interval_15_16,
                interval_16_17,
                interval_17_18,
                interval_18_19,
                interval_19_20,
                interval_20_21,
                interval_21_22,
                interval_22_23,
                interval_23_24
            )",
            addslashes($csv)
        );

        \DB::connection()->getpdo()->exec($query);

        /* Compute total operations time */
        $endTime = microtime(true);
        $elapsed = $endTime - $startTime;

        echo "Elapsed $elapsed" . PHP_EOL;
    }

    protected function testCsv()
    {
        $startTime = Utils::startTimer();

        $filePath = storage_path('app/volatile/lala2.csv');
        $handler = fopen($filePath, 'w+');

        foreach (range(1, 10000) as $i) {
            fputcsv($handler, ["record $i"]);
        }

        $elapsed = Utils::endTimer($startTime);
        Utils::dumpMessage($elapsed);

        /***********************************/

        $startTime = Utils::startTimer();

        $filePath = storage_path('app/volatile/lala3.csv');
        $csv = new \SplFileObject($filePath, 'w');

        foreach (range(1, 10000) as $i) {
            $csv->fputcsv([
                'foo',
                'bar',
                123,
            ]);
        }

        $elapsed = Utils::endTimer($startTime);
        Utils::dumpMessage($elapsed);

        /***********************************/

        $startTime = Utils::startTimer();

        $fileService = new FileService();

        foreach (range(1, 10000) as $i) {
            $fileService->writeRecordToCSVFile('lala4.csv', [
                'foo',
                'bar',
                123,
                $i,
            ]);
        }

        $elapsed = Utils::endTimer($startTime);
        Utils::dumpMessage($elapsed);

    }

}
