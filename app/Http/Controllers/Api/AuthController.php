<?php

namespace App\Http\Controllers\Api;

use Exception;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormat;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = $request->validate([
            'username' => 'required|exists:users,username',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();

            $at_expired = 60;
            $access_token = $user->createToken('access_token', ['access-api'], Carbon::now()->addMinutes($at_expired))->plainTextToken;

            $rt_expired = 30 * 24 * 60;
            $refresh_token = $user->createToken('refresh_token', ['issue-access-token'], Carbon::now()->addMinutes($rt_expired))->plainTextToken;

            if (!$user->hasRole(['atasan', 'member'])) {
                Auth::logout();
                return ResponseFormat::error(null, 'Akses ditolak. Silahkan login menggunakan akun karyawan.', 403);
            }

            $karyawan = $user->karyawan;
            $posisi = $karyawan->posisi[0];

            if (!$posisi) {
                Auth::logout();
                return ResponseFormat::error(null, 'Karyawan tidak memiliki posisi.', 403);
            }

            $currentShift = DB::table('attendance_karyawan_grup')->where('karyawan_id', $karyawan->id_karyawan)->orderByDesc('active_date')->first();

            if (!$currentShift) {
                Auth::logout();
                return ResponseFormat::error(null, 'Karyawan tidak memiliki shift aktif.', 403);
            }

            $data = [
                'username' => $user->username,
                'email' => $user->email,
                'organisasi_id' => $user->organisasi_id,
                'karyawan_id' => $karyawan?->id_karyawan,
                'nama' => $karyawan?->nama,
                'jabatan' => $posisi?->jabatan,
                'departemen' => $posisi?->departemen,
                'divisi' => $posisi?->divisi,
                'posisi' => $posisi,
                'shift' => $currentShift,
                'token' => $access_token,
                'refresh_token' => $refresh_token,
                'token_type' => 'Bearer',
            ];

            return ResponseFormat::success($data, 'Login berhasil');
        } else {
            return ResponseFormat::error(null, 'Username atau password salah.', 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return ResponseFormat::success(null, 'Logout berhasil');
    }

    public function refreshToken(Request $request)
    {
        try {
            $at_expired = 60;
            $new_token = $request->user()->createToken('access_token', ['access-api'], Carbon::now()->addMinutes($at_expired))->plainTextToken;


            $data = [
                'token' => $new_token,
                'token_type' => 'Bearer',
            ];

            return ResponseFormat::success($data, 'Token refreshed successfully');
        } catch (Exception $e) {
            return ResponseFormat::error(null, 'Gagal merefresh token.', 500);
        }
    }

    public function getProfile(Request $request)
    {
        try {
            $user = Auth::user();
            $karyawan = $user->karyawan;
            $posisi = $karyawan->posisi[0];

            if (!$posisi) {
                return ResponseFormat::error(null, 'Karyawan tidak memiliki posisi.', 403);
            }

            $currentShift = DB::table('attendance_karyawan_grup')->where('karyawan_id', $karyawan->id_karyawan)->orderByDesc('active_date')->first();

            if (!$currentShift) {
                return ResponseFormat::error(null, 'Karyawan tidak memiliki shift aktif.', 403);
            }

            $data = [
                'username' => $user->username,
                'email' => $user->email,
                'organisasi_id' => $user->organisasi_id,
                'karyawan_id' => $karyawan?->id_karyawan,
                'nama' => $karyawan?->nama,
                'jabatan' => $posisi?->jabatan,
                'departemen' => $posisi?->departemen,
                'divisi' => $posisi?->divisi,
                'posisi' => $posisi,
                'shift' => $currentShift,
            ];

            return ResponseFormat::success($data, 'Profile retrieved successfully');
        } catch (Exception $e) {
            return ResponseFormat::error(null, 'Terjadi kesalahan saat mengambil profile', 500);
        }
    }
}
