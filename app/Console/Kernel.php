<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Schedule the overdue points calculation every minute
        $schedule->command('calculate:overdue-points')
            ->everyMinute()
            ->sendOutputTo(storage_path('logs/calculate_overdue.log'));

        // Schedule the send deadline reminder command to run daily
        $schedule->command('send:deadline-reminder')
            ->daily()
            ->sendOutputTo(storage_path('logs/send_deadline_reminder.log'));  // Optional log file
    }
    
    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // Load commands from the default Commands directory
        $this->load(__DIR__ . '/Commands');

        // Load any custom console routes if required
        require base_path('routes/console.php');
    }
}
