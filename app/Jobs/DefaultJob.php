<?php

namespace Equinox\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class DefaultJob
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

}
