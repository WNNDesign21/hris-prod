<?php

namespace App\Http\Controllers\Cutie;

use Throwable;
use Carbon\Carbon;
use App\Models\Cutie;
use App\Models\Event;
use App\Models\Posisi;
use App\Helpers\Approval;
use Illuminate\Http\Request;
use App\Services\CutiService;
use App\Services\EventService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Helpers\SendWhatsappNotification;
use Illuminate\Support\Facades\Validator;

class CutieController extends Controller
{
    private $cutiService, $eventService;
    public function __construct(
        CutiService $cutiService,
        EventService $eventService
    )
    {
        $this->cutiService = $cutiService;
        $this->eventService = $eventService;
    }

    public function index()
    {
        if (auth()->user()->hasRole('atasan')){
            $jenis_cuti_title = 'Data Cuti by Jenis (Member) - '.Carbon::now()->format('F Y');
            $detail_cuti_title = 'Detail Cuti (Member) - '.date('Y');
        } else {
            $jenis_cuti_title = 'Data Cuti by Jenis (All Karyawan) - '.Carbon::now()->format('F Y');
            $detail_cuti_title = 'Detail Cuti (All Karyawan) - '.date('Y');
        }
        $dataPage = [
            'pageTitle' => "Cuti-E - Dashboard",
            'page' => 'cutie-dashboard',
            'jenis_cuti_title' => $jenis_cuti_title,
            'detail_cuti_title' => $detail_cuti_title,
        ];
        return view('pages.cuti-e.index', $dataPage);
    }

    public function get_data_cutie_calendar(){
        $organisasi_id = auth()->user()->organisasi_id;
        if(auth()->user()->hasRole('atasan')){
            $posisi = auth()->user()->karyawan->posisi;
            $id_posisi_members = Approval::GetMemberPosisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $dataFilter['members'] = $id_posisi_members;
            $dataFilter['karyawan_id'] = auth()->user()->karyawan->id_karyawan;
        } elseif (auth()->user()->hasRole('personalia')) {
            $dataFilter['organisasi_id'] = $organisasi_id;
        }

        $filterEvent['jenis_event'] = ['CB', 'LN'];
        $filterEvent['organisasi_id'] = $organisasi_id;
        $event = $this->eventService->getWithFilters($filterEvent, ['*'])->get();
        $cutie = $this->cutiService->getCalendarData($dataFilter);
        $data = [];

        if($event){
            foreach ($event as $e) {
                if($e->jenis_event == 'CB'){
                    $className = 'bg-danger';
                    $jenisEvent = 'CUTI BERSAMA';
                } elseif ($e->jenis_event == 'LN') {
                    $className = 'bg-danger';
                    $jenisEvent = 'LIBUR NASIONAL';
                } else {
                    $jenisEvent = 'EVENT PERUSAHAAN';
                    $className = 'bg-info';
                }
                $data[] = [
                    'title' => $e->keterangan,
                    'start' => $e->tanggal_mulai,
                    'end' => $e->tanggal_selesai !== $e->tanggal_mulai ? Carbon::parse($e->tanggal_selesai)->addDay()->format('Y-m-d') : $e->tanggal_selesai,
                    'className' => $className,
                    'nama_karyawan' => 'Seluruh Karyawan',
                    'karyawan_pengganti' => '-',
                    'jenis_cuti' => $jenisEvent,
                    'rencana_mulai_cuti' => Carbon::parse($e->tanggal_mulai)->format('d M Y'),
                    'rencana_selesai_cuti' => Carbon::parse($e->tanggal_selesai)->format('d M Y'),
                    'alasan_cuti' => $e->keterangan,
                    'durasi_cuti' => $e->durasi.' Hari',
                    'status_cuti' => $e->tanggal_mulai > now() ? 'SCHEDULED' : 'COMPLETED',
                    'attachment' => 'No Attachment Needed',
                    'aktual_mulai_cuti' => $e->tanggal_mulai > now() ? '-' : Carbon::parse($e->tanggal_mulai)->format('d M Y'),
                    'aktual_selesai_cuti' => $e->tanggal_selesai > now() ? '-' : Carbon::parse($e->tanggal_selesai)->format('d M Y'),
                ];
            }
        }

        if($cutie){
            foreach ($cutie as $c) {

                $status_cuti = 'NEED APPROVE';
                if ($c->status_dokumen == 'WAITING') {
                    $classname = 'bg-primary';
                } elseif(Carbon::now()->format('Y-m-d') < $c->rencana_mulai_cuti){
                    $statusCuti = 'SCHEDULED';
                    $classname = 'bg-warning';
                } elseif (Carbon::now()->between(Carbon::createFromFormat('Y-m-d', $c->rencana_mulai_cuti), Carbon::createFromFormat('Y-m-d', $c->rencana_selesai_cuti))) {
                    $statusCuti = 'ON LEAVE';
                    $classname = 'bg-secondary';
                } elseif (Carbon::now()->format('Y-m-d') > $c->rencana_mulai_cuti) {
                    $statusCuti = 'COMPLETED';
                    $classname = 'bg-success';
                }

                $data[] = [
                    'title' => $c->jenis_cuti.' - '.$c->karyawan->nama,
                    'start' => $c->rencana_mulai_cuti,
                    'end' => $c->rencana_selesai_cuti !== $c->rencana_mulai_cuti ? Carbon::parse($c->rencana_selesai_cuti)->addDay()->format('Y-m-d') : $c->rencana_selesai_cuti,
                    'className' => $classname,
                    'nama_karyawan' => $c->karyawan->nama,
                    'karyawan_pengganti' => $c->karyawan_pengganti_id ? $c->karyawanPengganti->nama : '-',
                    'jenis_cuti' => 'CUTI '.$c->jenis_cuti,
                    'rencana_mulai_cuti' => Carbon::parse($c->rencana_mulai_cuti)->format('d M Y'),
                    'rencana_selesai_cuti' => Carbon::parse($c->rencana_selesai_cuti)->format('d M Y'),
                    'alasan_cuti' => $c->alasan_cuti,
                    'durasi_cuti' => $c->durasi_cuti.' Hari',
                    'status_cuti' => $statusCuti,
                    'attachment' => $c->attachment ? '<a href="'.asset('storage/'.$c->attachment).'" target="_blank">Lihat</a>' : 'No Attachment Needed',
                ];
            }
        }
        return response()->json($data, 200);
    }

    // public function get_data_cuti_detail_chart(){
    //     $organisasi_id = auth()->user()->organisasi_id;

    //     $data['scheduled'] = [];
    //     $data['onleave'] = [];
    //     $data['canceled'] = [];
    //     $data['completed'] = [];
    //     $data['rejected'] = [];
    //     $data['total'] = [];

    //     $month = date('m');
    //     $year = date('Y');
    //     $month_array = ['01','02','03','04','05','06','07','08','09','10','11','12'];

    //     if(auth()->user()->hasRole('atasan')){
    //         $posisi = auth()->user()->karyawan->posisi;
    //         $id_posisi_members = Approval::GetMemberPosisi($posisi);

    //         foreach ($posisi as $ps){
    //             $index = array_search($ps->id_posisi, $id_posisi_members);
    //             array_splice($id_posisi_members, $index, 1);
    //         }

    //         $members = $id_posisi_members;
    //     }


    //     for ($i = 0; $i <= 11; $i++) {

    //         //SCHEDULED
    //         $scheduledCount = Cutie::where('status_cuti', 'SCHEDULED')
    //             ->whereYear('rencana_mulai_cuti', $year)
    //             ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

    //         if (isset($members)) {
    //             $scheduledCount = $scheduledCount->whereHas('karyawan.posisi', function($query) use ($members) {
    //                 $query->whereIn('id_posisi', $members);
    //             });
    //         } else {
    //             $scheduledCount = $scheduledCount->organisasi($organisasi_id);
    //         }

    //         $scheduledCount = $scheduledCount->count();

    //         //ONLEAVE
    //         $onleaveCount = Cutie::where('status_cuti', 'ON LEAVE')
    //             ->where('status_dokumen', 'APPROVED')
    //             ->whereYear('rencana_mulai_cuti', $year)
    //             ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

    //         if (isset($members)) {
    //             $onleaveCount = $onleaveCount->whereHas('karyawan.posisi', function($query) use ($members) {
    //                 $query->whereIn('id_posisi', $members);
    //             });
    //         } else {
    //             $onleaveCount = $onleaveCount->organisasi($organisasi_id);
    //         }
    //         $onleaveCount = $onleaveCount->count();

    //         //COMPLETED
    //         $completedCount = Cutie::where('status_cuti', 'COMPLETED')
    //             ->where('status_dokumen', 'APPROVED')
    //             ->whereYear('rencana_mulai_cuti', $year)
    //             ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

    //         if (isset($members)) {
    //             $completedCount = $completedCount->whereHas('karyawan.posisi', function($query) use ($members) {
    //                 $query->whereIn('id_posisi', $members);
    //             });
    //         } else {
    //             $completedCount = $completedCount->organisasi($organisasi_id);
    //         }
    //         $completedCount = $completedCount->count();

    //         //CANCELED
    //         $canceledCount = Cutie::where('status_cuti', 'CANCELED')
    //             ->whereYear('rencana_mulai_cuti', $year)
    //             ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

    //         if (isset($members)) {
    //             $canceledCount = $canceledCount->whereHas('karyawan.posisi', function($query) use ($members) {
    //                 $query->whereIn('id_posisi', $members);
    //             });
    //         } else {
    //             $canceledCount = $canceledCount->organisasi($organisasi_id);
    //         }
    //         $canceledCount = $canceledCount->count();

    //         //REJECTED
    //         $rejectedCount = Cutie::where('status_dokumen', 'REJECTED')
    //             ->whereYear('rencana_mulai_cuti', $year)
    //             ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

    //         if (isset($members)) {
    //             $rejectedCount = $rejectedCount->whereHas('karyawan.posisi', function($query) use ($members) {
    //                 $query->whereIn('id_posisi', $members);
    //             });
    //         } else {
    //             $rejectedCount = $rejectedCount->organisasi($organisasi_id);
    //         }
    //         $rejectedCount = $rejectedCount->count();

    //         //UNLEGALIZED
    //         $unlegalizedCount = Cutie::whereNull('legalized_by')
    //             ->whereNull('rejected_by')
    //             ->where('status_cuti', '!=', 'CANCELED')
    //             ->whereYear('rencana_mulai_cuti', $year)
    //             ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

    //         if (isset($members)) {
    //             $unlegalizedCount = $unlegalizedCount->whereHas('karyawan.posisi', function($query) use ($members) {
    //                 $query->whereIn('id_posisi', $members);
    //             });
    //         } else {
    //             $unlegalizedCount = $unlegalizedCount->organisasi($organisasi_id);
    //         }
    //         $unlegalizedCount = $unlegalizedCount->count();

    //         //TOTAL
    //         $totalCount = Cutie::whereYear('rencana_mulai_cuti', $year)
    //         ->whereMonth('rencana_mulai_cuti', $month_array[$i]);

    //         if (isset($members)) {
    //             $totalCount = $totalCount->whereHas('karyawan.posisi', function($query) use ($members) {
    //                 $query->whereIn('id_posisi', $members);
    //             });
    //         } else {
    //             $totalCount = $totalCount->organisasi($organisasi_id);
    //         }
    //         $totalCount = $totalCount->count();

    //         $data['scheduled'][] = $scheduledCount;
    //         $data['onleave'][] = $onleaveCount;
    //         $data['completed'][] = $completedCount;
    //         $data['canceled'][] = $canceledCount;
    //         $data['rejected'][] = $rejectedCount;
    //         $data['unlegalized'][] = $unlegalizedCount;
    //         $data['total'][] = $totalCount;
    //     }

    //     return response()->json(['data' => $data],200);
    // }

    // public function get_data_jenis_cuti_monthly_chart(){
    //     $month = date('m');
    //     $year = date('Y');
    //     $organisasi_id = auth()->user()->organisasi_id;

    //     if(auth()->user()->hasRole('atasan')){
    //         $posisi = auth()->user()->karyawan->posisi;
    //         $id_posisi_members = Approval::GetMemberPosisi($posisi);

    //         foreach ($posisi as $ps){
    //             $index = array_search($ps->id_posisi, $id_posisi_members);
    //             array_splice($id_posisi_members, $index, 1);
    //         }

    //         $members = $id_posisi_members;
    //     }


    //     $monthly_pribadi = Cutie::where('jenis_cuti', 'PRIBADI')
    //         ->whereYear('rencana_mulai_cuti', $year)
    //         ->whereMonth('rencana_mulai_cuti', $month)
    //         ->where('status_cuti', '!=' ,'CANCELED')
    //         ->where('status_dokumen', 'APPROVED');

    //         if (isset($members)) {
    //             $monthly_pribadi = $monthly_pribadi->whereHas('karyawan.posisi', function($query) use ($members) {
    //                 $query->whereIn('id_posisi', $members);
    //             });
    //         } else{
    //             $monthly_pribadi = $monthly_pribadi->organisasi($organisasi_id);
    //         }
    //     $monthly_pribadi = $monthly_pribadi->count();

    //     $monthly_khusus = Cutie::where('jenis_cuti', 'KHUSUS')
    //         ->whereYear('rencana_mulai_cuti', $year)
    //         ->whereMonth('rencana_mulai_cuti', $month)
    //         ->where('status_cuti', '!=' ,'CANCELED')
    //         ->where('status_dokumen', 'APPROVED');

    //         if (isset($members)) {
    //             $monthly_khusus = $monthly_khusus->whereHas('karyawan.posisi', function($query) use ($members) {
    //                 $query->whereIn('id_posisi', $members);
    //             });
    //         } else {
    //             $monthly_khusus = $monthly_khusus->organisasi($organisasi_id);
    //         }
    //     $monthly_khusus = $monthly_khusus->count();
    //     $data = [$monthly_pribadi, $monthly_khusus];
    //     return response()->json(['data' => $data], 200);
    // }

    // function send_whatsapp($id_karyawan, $for_posisi, $message, $organisasi_id)
    // {
    //     $karyawan = Karyawan::find($id_karyawan);
    //     $karyawanPosisi = Posisi::where('id_posisi', $for_posisi)->first();
    //     $phoneNumbers = [];

    //     if ($karyawanPosisi){
    //         $karyawanPosisis = $karyawanPosisi->karyawan;
    //         foreach($karyawanPosisis as $kp){
    //             $phoneNumber = $kp->no_telp;
    //             if (substr($phoneNumber, 0, 1) == '0') {
    //                 $phoneNumber = '62' . substr($phoneNumber, 1);
    //             }
    //             $phoneNumbers[] = $phoneNumber . '@c.us';
    //         }

    //         if(count($phoneNumbers) > 0){
    //             $phoneNumber = $phoneNumbers;
    //         } else {
    //             $phoneNumber = $phoneNumbers[0];
    //         }

    //         SendWhatsappNotification::send($message, $organisasi_id, $phoneNumber);
    //     }
    // }
}
