<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        $schedule->command('schedule:activity-reminder')
                 ->timezone('Asia/Jakarta')
                 ->at('10:00');

        $schedule->command('schedule:activity-reminder')
                 ->timezone('Asia/Jakarta')
                 ->at('14:00');

        $schedule->command('catchup:lead-status')
                 ->timezone('Asia/Jakarta')
                 ->at('00:00');

        $schedule->command('queue:prune-batches')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
