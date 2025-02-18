<?php

use App\Jobs\RollingShiftGroupJob;
use Illuminate\Foundation\Application;
use App\Http\Middleware\IzineMiddleware;
use App\Http\Middleware\PiketMiddleware;
use App\Http\Middleware\LembureMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\NotificationMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'notifikasi' => NotificationMiddleware::class,
            'lembure' => LembureMiddleware::class,
            'izine' => IzineMiddleware::class,
            'piket' => PiketMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('cutie:update-status-completed')
            ->dailyAt('21:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->onOneServer();

        $schedule->command('cutie:update-status-onleave')
            ->dailyAt('22:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->onOneServer();

        $schedule->command('cutie:automatic-reject-cuti')
            ->dailyAt('23:00')
            ->withoutOverlapping()
            ->runInBackground()
            ->onOneServer();

        $schedule->command('cutie:automatic-reset-cuti')
            ->dailyAt('23:30')
            ->withoutOverlapping()
            ->runInBackground()
            ->onOneServer();
            
        $schedule->job(new RollingShiftGroupJob)->dailyAt('23:45')
            ->withoutOverlapping()
            ->onOneServer();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
