<?php

namespace App\Console;

use App\Console\Commands\SYSTEAMGenerateSecretCommand;
use App\Console\Commands\SYSTEAMInitRBAC;
use App\Console\Commands\SYSTEAMInstallCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SYSTEAMInstallCommand::class,
        SYSTEAMInitRBAC::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

    }
}
