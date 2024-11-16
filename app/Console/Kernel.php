<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
   
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
    }
    protected $commands = [
        // ...
        \App\Console\Commands\CleanPDFs::class,
        \App\Console\Commands\DeletePublicPDFs::class,
        // ...
    ];


    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
