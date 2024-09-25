<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $notification = [];
        $today = date('Y-m-d');
        $user = auth()->user();

        if($user->hasRole('personalia') || $user->hasRole('super user')){
            $tenggang_karyawans = Karyawan::where('status_karyawan', 'AKTIF')
                ->whereRaw('(tanggal_selesai - ?) <= 30', [$today])
                ->selectRaw('*, (tanggal_selesai - ?) as jumlah_hari', [$today])
                ->get();
        } elseif ($user->hasRole('atasan')){
            $posisi = $user->karyawan->posisi;
            $id_posisi = [];
            foreach ($posisi as $ps){
                foreach ($ps->children as $child){
                    $id_posisi[] = $child->id_posisi;
                }
            }

            $tenggang_karyawans = Karyawan::where('status_karyawan', 'AKTIF')
                ->whereRaw('(tanggal_selesai - ?) <= 30', [$today])
                ->whereHas('posisi', function($query) use ($id_posisi) {
                    $query->whereIn('posisi_id', $id_posisi);
                })
                ->selectRaw('*, (tanggal_selesai - ?) as jumlah_hari', [$today])
                ->get();
        } else {
            $tenggang_karyawans = Karyawan::where('status_karyawan', 'AKTIF')->where('id_karyawan', $user->karyawan->id_karyawan)
                ->whereRaw('(tanggal_selesai - ?) <= 30', [$today])
                ->selectRaw('*, (tanggal_selesai - ?) as jumlah_hari', [$today])
                ->get();
        }

        $notification = [
            'count_tenggang_karyawan' => $tenggang_karyawans->count(),
            'list' => $tenggang_karyawans
        ];
        view()->share('notification', $notification);
        return $next($request);
    }
}
