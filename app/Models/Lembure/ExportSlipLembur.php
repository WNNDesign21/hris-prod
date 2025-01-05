<?php

namespace App\Models\Lembure;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExportSlipLembur extends Model
{
    use HasFactory;

    protected $table = 'export_slip_lemburs';
    protected $primaryKey = 'id_export_slip_lembur';

    protected $fillable = [
        'organisasi_id',
        'departemen_id',
        'periode',
        'status',
        'attachment',
        'message'
    ];

    protected $casts = [
        'periode' => 'date',
        'status' => 'string',
        'attachment' => 'string',
        'message' => 'string',
    ];

    public function organisasi()
    {
        return $this->belongsTo(Organisasi::class, 'organisasi_id', 'id_organisasi');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'departemen_id', 'id_departemen');
    }

    private static function _query($dataFilter)
    {

        $data = self::select(
            'export_slip_lemburs.id_export_slip_lembur',
            'export_slip_lemburs.organisasi_id',
            'export_slip_lemburs.departemen_id',
            'export_slip_lemburs.periode',
            'export_slip_lemburs.status',
            'export_slip_lemburs.attachment',
            'export_slip_lemburs.message',
            'export_slip_lemburs.created_at',
            'export_slip_lemburs.updated_at',
            'departemens.nama as departemen',
        )
        ->leftJoin('departemens', 'departemens.id_departemen', 'export_slip_lemburs.departemen_id');
        $data->where('export_slip_lemburs.organisasi_id', auth()->user()->organisasi_id);

        if (isset($dataFilter['status'])) {
            $data->where('export_slip_lemburs.status', $dataFilter['status']);
        }

        if (isset($dataFilter['departemen_id'])) {
            $data->where('export_slip_lemburs.departemen_id', $dataFilter['departemen_id']);
        }

        if (isset($dataFilter['periode'])) {
            $data->where('export_slip_lemburs.periode', $dataFilter['periode']);
        }

        if (isset($dataFilter['search'])) {
            $search = $dataFilter['search'];
            $data->where(function ($query) use ($search) {
                $query->where('departemens.nama', 'ILIKE', "%{$search}%")
                    ->orWhere('export_slip_lemburs.periode', 'ILIKE', "%{$search}%")
                    ->orWhere('export_slip_lemburs.status', 'ILIKE', "%{$search}%")
                    ->orWhere('export_slip_lemburs.message', 'ILIKE', "%{$search}%");
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
