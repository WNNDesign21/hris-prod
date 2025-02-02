<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\IzineMiddleware;
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
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('cutie:update-status-onleave')->dailyAt('08:00')->runInBackground();
        $schedule->command('cutie:update-status-completed')->dailyAt('21:00')->runInBackground();
        $schedule->command('cutie:automatic-reject-cuti')->dailyAt('23:00')->runInBackground();
        $schedule->command('cutie:automatic-reset-cuti')->dailyAt('23:30')->runInBackground();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
