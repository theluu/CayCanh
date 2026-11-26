<?php

use Illuminate\Console\Scheduling\Schedule;

function scheduled(): array
{
    return collect(app(Schedule::class)->events())
        ->mapWithKeys(fn ($event) => [trim(preg_replace('/^.*artisan["\']?\s+/', '', $event->command)) => $event->expression])
        ->all();
}

it('schedules database backups daily and full backups weekly', function () {
    $events = scheduled();

    expect($events['backup:run --only-db'] ?? null)->toBe('0 2 * * *')
        ->and($events['backup:run'] ?? null)->toBe('0 3 * * 0')
        ->and($events)->toHaveKey('backup:clean')
        ->and($events)->toHaveKey('backup:monitor');
});

it('schedules care, sitemap and maintenance jobs', function () {
    expect(scheduled())->toMatchArray([
        'care:generate' => '0 1 * * *',
        'care:remind' => '30 6 * * *',
        'sitemap:generate' => '30 2 * * *',
    ]);
});

it('keeps seven daily and four weekly backups', function () {
    expect(config('backup.cleanup.default_strategy.keep_daily_backups_for_days'))->toBe(7)
        ->and(config('backup.cleanup.default_strategy.keep_weekly_backups_for_weeks'))->toBe(4)
        ->and(config('backup.backup.destination.disks'))->toContain('local');
});
