<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Karyawan;
use App\Models\Organisasi;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'organisasi_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function karyawan()
    {
        return $this->hasOne(Karyawan::class, 'user_id', 'id');
    }

    // private static function _query($dataFilter)
    // {

    //     $data = self::select(
    //         'id',
    //         'username',
    //         'email',
    //         'organisasis.nama as organisasi',
    //         'organisasi_id',
    //     )->leftJoin('organisasis', 'organisasis.id_organisasi', 'users.organisasi_id');

    //     if (isset($dataFilter['search'])) {
    //         $search = $dataFilter['search'];
    //         $data->where(function ($query) use ($search) {
    //             $query->where('username', 'ILIKE', "%{$search}%")
    //                 ->orWhere('email', 'ILIKE', "%{$search}%")
    //                 ->orWhere('organisasis.nama', 'ILIKE', "%{$search}%")
    //                 ->orWhere('organisasi_id', 'ILIKE', "%{$search}%");
    //         });
    //     }

    //     $result = $data;
    //     return $result;
    // }
    private static function _query($dataFilter)
    {
        $data = self::select(
            'users.id',
            'users.username',
            'users.email',
            'organisasis.nama as organisasi',
            'users.organisasi_id',
            'roles.name as role',
        )->leftJoin('organisasis', 'organisasis.id_organisasi', 'users.organisasi_id')
        ->leftJoin('model_has_roles', 'model_has_roles.model_id', 'users.id')
        ->leftJoin('roles', 'roles.id', 'model_has_roles.role_id')
        ->where('model_has_roles.model_type', 'App\Models\User');

        $data->whereIn('roles.name', ['personalia', 'security']);

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('users.username', 'ILIKE', "%{$search}%")
                    ->orWhere('users.email', 'ILIKE', "%{$search}%")
                    ->orWhere('organisasis.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('users.organisasi_id', 'ILIKE', "%{$search}%");
            });
        }

        $result = $data;
        return $result;
    }

    public static function getData($dataFilter, $settings)
    {
        return self::_query($dataFilter)->offset($settings['start'])
            ->limit($settings['limit'])
            ->orderBy($settings['order'], $settings['dir'])
            ->get();
    }

    public static function countData($dataFilter)
    {
        return self::_query($dataFilter)->get()->count();
    }
}
