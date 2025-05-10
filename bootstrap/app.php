<?php

use App\Jobs\ResetCutiJob;
use App\Jobs\UpdateCutiJob;
use Illuminate\Http\Request;
use App\Jobs\RollingShiftGroupJob;
use Illuminate\Foundation\Application;
use App\Http\Middleware\IzineMiddleware;
use App\Http\Middleware\LembureMiddleware;
use Illuminate\Console\Scheduling\Schedule;
use App\Http\Middleware\TugasluarMiddleware;
use App\Http\Middleware\NotificationMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use App\Http\Middleware\NotificationKSKMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

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
            'abilities' => CheckAbilities::class,
            'ability' => CheckForAnyAbility::class,
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
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
        $schedule->job(new ResetCutiJob($today, $month, $day))->dailyAt('00:00');
        $schedule->job(new RollingShiftGroupJob)->sundays()->at('21:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Access Denied'
                ], 403);
            }
        });
    })->create();
