<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('queue:work')->everyMinute()->withoutOverlapping()->then(function () {
            Log::info('Queue worker finished');
        });

        if (DB::table('failed_jobs')->count() > 0) {
            $schedule->command('queue:retry all')->everyMinute()->withoutOverlapping()->then(function () {
                Log::info('Queue retry finished');
            });
        }
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
