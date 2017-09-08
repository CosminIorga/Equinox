<?php

namespace Equinox\Console\Commands;

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

                break;
            case "modify":

                break;
            case "fetch":

                break;
            default:
                dump( "I did nothing");
                break;
        }
    }


}
