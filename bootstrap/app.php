<?php

use Illuminate\Foundation\Application;
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
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('cutie:update-status-onleave')->hourly();
        $schedule->command('cutie:update-status-completed')->everyFiveMinutes()->between('20:30', '23:59');
        $schedule->command('cutie:automatic-reject-cuti')->everyFiveMinutes()->between('20:30', '23:59');
        $schedule->command('cutie:automatic-reset-cuti')->everyFiveMinutes()->between('20:30', '23:59');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
