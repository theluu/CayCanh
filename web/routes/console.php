<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('care:generate')->dailyAt('01:00');
Schedule::command('care:remind')->dailyAt('06:30');
Schedule::command('sitemap:generate')->dailyAt('02:30');

Schedule::command('backup:run --only-db')->dailyAt('02:00')->withoutOverlapping();
Schedule::command('backup:run')->weeklyOn(0, '03:00')->withoutOverlapping();
Schedule::command('backup:clean')->dailyAt('04:00');
Schedule::command('backup:monitor')->dailyAt('04:30');
Schedule::command('queue:prune-failed --hours=720')->daily();
