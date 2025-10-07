<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\KSK\KSK;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use App\Models\KSK\DetailKSK;
use App\Models\KSK\CleareanceDetail;
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
        $total_release_cleareance = 0;
        $total_approval_cleareance = 0;
        $total_tindak_lanjut = 0;

        $dataFilter = [];
        if(auth()->user()->hasRole('personalia')) {
            $total_release_ksk = Karyawan::countDataKSK($dataFilter);
            $total_release_cleareance = DetailKSK::where('organisasi_id', auth()->user()->organisasi_id)->where('status_ksk', 'PHK')->whereNull('cleareance_id')->count();
            $total_tindak_lanjut = DetailKSK::countNeedAction($dataFilter);
        } else {
            if (!auth()->user()->hasRole('security')) {
                $total_approval_cleareance = CleareanceDetail::where('confirmed_by_id', auth()->user()->karyawan->id_karyawan)->where('is_clear', 'N')->count();
            }
        }

        if (!auth()->user()->hasRole('security')) {
            $total_approval_ksk = KSK::countDataKSK($dataFilter);
        }

        $datas = [
            'total_release_ksk' => $total_release_ksk,
            'total_approval_ksk' => $total_approval_ksk,
            'total_release_cleareance' => $total_release_cleareance,
            'total_approval_cleareance' => $total_approval_cleareance,
            'total_tindak_lanjut' => $total_tindak_lanjut,
        ];

        view()->share('ksk', $datas);
        return $next($request);
    }
}
