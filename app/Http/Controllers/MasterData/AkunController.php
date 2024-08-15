<?php

namespace App\Http\Controllers\MasterData;

use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AkunController extends Controller
{
    public function store(Request $request)
    {
        $dataValidate = [
            'username_akunEdit' => ['required'],
            'password_akunEdit' => ['required'],
            'email_akunEdit' => ['required','email'],
        ];

        $validator = Validator::make(request()->all(), $dataValidate);
    
        if ($validator->fails()) {
            return response()->json(['message' => 'Fill your input correctly!'], 402);
        }

        $username = $request->username_akunEdit;
        $password = $request->password_akunEdit;
        $email = $request->email_akunEdit;
        $id_user = $request->id_akunEdit;
        $id_karyawan = $request->id_karyawanAkunEdit;

        DB::beginTransaction();
        try{

            if($id_user){
                $user = User::find($id_user);
                $karyawan = Karyawan::find($id_karyawan);

                $user->update([
                    'username' => $username,
                    'password' => Hash::make($password),
                    'email' => $email,
                ]);

                DB::commit();
                return response()->json(['message' => 'Akun dari karyawan '.$karyawan->nama.' Berhasil Diubah!'],200);
            } else {
                $user = User::create([
                    'username' => $username,
                    'password' => Hash::make($password),
                    'email' => $email,
                ]);

                $karyawan = Karyawan::find($id_karyawan);
                $karyawan->update([
                    'user_id' => $user->id,
                ]);

                DB::commit();
                return response()->json(['message' => 'Akun dari karyawan '.$karyawan->nama.' Berhasil Ditambahkan!'],200);
            }

            return response()->json(['message' => 'Akun Ditambahkan!'],200);
        } catch(Throwable $error){
            DB::rollBack();
            return response()->json(['message' => $error->getMessage()], 500);
        }
    }

    public function get_data_detail_akun(string $id_user)
    {
        $user = User::find($id_user);
        $detail = [];
        if($user){
            $detail = [
                'id_user' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ];
            return response()->json(['data' => $detail], 200);
        } else {
            return response()->json(['message' => 'Data Karyawan tidak ditemukan!'], 404);
        }

    }
}
