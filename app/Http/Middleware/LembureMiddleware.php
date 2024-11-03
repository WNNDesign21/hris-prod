<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Posisi;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LembureMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $has_leader = false;
        if(auth()->user()->karyawan && auth()->user()->karyawan->posisi){
            $posisi = auth()->user()->karyawan->posisi;
            $has_leader = $this->has_leader_head($posisi);
        }

        $lembure = [
            'has_leader' => $has_leader,
            'is_leader' => $this->is_leader(),
        ];
        
        view()->share('lembure', $lembure);
        return $next($request);
    }

    function is_leader(){
        $is_leader = false;
        if(auth()->user()->karyawan && auth()->user()->karyawan->posisi[0]->jabatan_id == 5){
            $is_leader = true;
        }
        return $is_leader;
    }

    function has_leader_head($posisi)
    {
        $has_leader = false;
        if($posisi){
            foreach($posisi as $pos){
                $parent_posisi_ids = $this->get_parent_posisi($pos);
                if(!empty($parent_posisi_ids)){
                    foreach ($parent_posisi_ids as $parent_id){
                        if($parent_id !== 0){
                            if(Posisi::where('id_posisi', $parent_id)->first()->jabatan_id == 5){
                                $has_leader = true;
                            }
                        }
                    }
                }
            }
        } else {
            return response()->json(['message' => 'Anda tidak memiliki posisi, silahkan hubungi HRD'], 200);
        }

        return $has_leader;
    } 

    function get_parent_posisi($posisi)
    {
        $data = [];
        if ($posisi->parent_id !== 0) {
            $parent = Posisi::find($posisi->parent_id);
            $data = array_merge($data, $this->get_parent_posisi($parent));
        }
        $data[] = $posisi->parent_id;
        return $data;
    }
}
