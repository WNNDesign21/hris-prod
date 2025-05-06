<?php

use App\Jobs\ResetCutiJob;
use App\Jobs\UpdateCutiJob;
use App\Jobs\RollingShiftGroupJob;
use Illuminate\Foundation\Application;
use App\Http\Middleware\IzineMiddleware;
use App\Http\Middleware\LembureMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\TugasluarMiddleware;
use App\Http\Middleware\NotificationMiddleware;
use App\Http\Middleware\NotificationKSKMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
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
            'tugasluare' => TugasluarMiddleware::class,
            'ksk' => NotificationKSKMiddleware::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $today = date('Y-m-d');
        $month = now()->month;
        $day = now()->day;
        $schedule->job(new UpdateCutiJob($today))->dailyAt('10:00');
        $schedule->job(new ResetCutiJob($today, $month, $day))->dailyAt('16:30');
        $schedule->job(new RollingShiftGroupJob)->sundays()->at('23:45');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
