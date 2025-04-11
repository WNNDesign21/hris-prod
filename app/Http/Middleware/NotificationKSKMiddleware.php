<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\KSK\KSK;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationKSKMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $total_release_ksk = 0;
        $total_approval_ksk = 0;

        $dataFilter = [];
        if(auth()->user()->hasRole('personalia')) {
            $total_release_ksk = Karyawan::countDataKSK($dataFilter);
        }

        $total_approval_ksk = KSK::countDataKSK($dataFilter);

        $datas = [
            'total_release_ksk' => $total_release_ksk,
            'total_approval_ksk' => $total_approval_ksk,
        ];

        view()->share('ksk', $datas);
        return $next($request);
    }
}
