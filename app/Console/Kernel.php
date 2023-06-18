<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands;
use App\Jobs\SchedulePostJob;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\WriteLog::Class,
        Commands\SchedulePostCommand::Class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        // $schedule->command('app:schedule-post-command')
        // ->everyMinute()
        // ->runInBackground();
        // $schedule->command('queue:work')->everyMinute();


        // $schedule->command('writelog:info')
        // ->everyMinute()
        // ->runInBackground();



        // $schedule->job(new SchedulePostJob())->everyMinute();
        // $schedule->command('writelog:info')->everyFiveMinutes();
        
        // $schedule->command('queue:work --tries=3 --stop-when-empty')->everyMinute();
        // $schedule->command('writelog:info')->everyMinute();
        // $schedule->command('inspire')->hourly();
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
