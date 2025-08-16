<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule system maintenance and backups
Schedule::command('db:backup --notify')->daily()->at('02:00');
Schedule::command('system:health-check --notify')->hourly();
Schedule::command('system:maintenance')->weekly()->sundays()->at('03:00');
Schedule::command('backup:clean')->daily()->at('04:00');
