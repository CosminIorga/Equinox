<?php

namespace Equinox\Console\Commands;

use Carbon\Carbon;
use Equinox\Jobs\CreateNewStorage;
use Equinox\Jobs\ModifyStorageData;
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
            $range = 10;

            $clients = range("A", "Z");
            $destinations = range(1, 30);
            $referenceDate = "2017-05-15";


            foreach (range(1, $range) as $index) {
                yield [
                    'is_full_record' => (bool) rand(0, 1),
                    'client' => "company_" . $clients[rand(0, count($clients) - 1)],
                    'carrier' => "company_" . $clients[rand(0, count($clients) - 1)],
                    'destination' => "destination_" . $destinations[rand(0, count($destinations) - 1)],
                    'start_date' => (new Carbon($referenceDate))
                        ->subDays(rand(0, 2))
                        ->setTime(rand(0, 24), rand(0, 24), 0),
                    'duration' => rand(0, 100),
                    'cost' => rand(100, 240) / 100,
                ];
            }
        };


        $job = new ModifyStorageData('insert', $generator());

        dispatch_now($job);

    }

}
