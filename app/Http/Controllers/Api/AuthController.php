<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\ResponseFormat;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Gather user profile data for API response.
     *
     * @param  \App\Models\User  $user
     * @return array
     */
    private function gatherUserData(User $user): array
    {
        $karyawan = null;
        $posisi = null;
        $currentShift = null;

        if ($user->hasRole(['atasan', 'member'])) {
            $karyawan = $user->karyawan;
            if ($karyawan) {
                $posisi = $karyawan->posisi[0] ?? null;
                $currentShift = DB::table('attendance_karyawan_grup')
                    ->where('karyawan_id', $karyawan->id_karyawan)
                    ->orderByDesc('active_date')
                    ->first();
            }
        }

        return [
            'username' => $user->username,
            'email' => $user->email,
            'organisasi_id' => $user->organisasi_id,
            'roles' => $user->getRoleNames()->toArray(),
            'karyawan_id' => $karyawan?->id_karyawan,
            'nama' => $karyawan?->nama,
            'posisi' => $posisi,
            'shift' => $currentShift,
        ];
    }

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

            if ($user->hasRole(['atasan', 'member'])) {
                if (!isset($user->karyawan->posisi[0])) {
                    Auth::logout();
                    return ResponseFormat::error(null, 'Karyawan tidak memiliki posisi.', 403);
                }
            } elseif (!$user->hasRole('personalia')) {
                Auth::logout();
                return ResponseFormat::error(null, 'Akses ditolak. Silahkan login menggunakan akun karyawan / HRD.', 403);
            }

            // Mengambil data profil menggunakan method privat
            $data = $this->gatherUserData($user);

            // Menambahkan token ke response
            $data += [
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

            return ResponseFormat::success($data, 'Token berhasil diperbarui');
        } catch (\Throwable $e) {
            // Log error jika perlu
            return ResponseFormat::error(null, 'Gagal memperbarui token.', 500);
        }
    }

    public function getProfile(Request $request)
    {
        try {
            $data = $this->gatherUserData($request->user());
            return ResponseFormat::success($data, 'Profil berhasil diambil');
        } catch (\Throwable $e) {
            // Log error jika perlu
            return ResponseFormat::error(null, 'Terjadi kesalahan saat mengambil profil', 500);
        }
    }
}
