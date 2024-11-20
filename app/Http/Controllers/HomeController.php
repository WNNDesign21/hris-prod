<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Cutie;
use App\Models\Kontrak;
use App\Models\Lembure;
use App\Models\Karyawan;
use App\Models\DetailLembur;
use Illuminate\Http\Request;
use App\Models\SettingLembur;
use App\Models\SettingLemburKaryawan;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $profile = auth()->user()?->karyawan;
        $dataProfile = [];
        $dataKontrak = [];
        if($profile){
            if($profile?->status_karyawan == 'AT'){
                $status = 'AKTIF';
            } elseif($profile?->status_karyawan == 'MD'){
                $status = 'MENGUNDURKAN DIRI';
            } elseif($profile?->status_karyawan == 'PS'){
                $status = 'PENSIUN';
            } elseif($profile?->status_karyawan == 'HK'){
                $status = 'HABIS KONTRAK';
            } else {
                $status = 'TERMINASI';
            }

            $dataProfile = [
                'ni_karyawan' => $profile?->ni_karyawan,
                'foto' => $profile?->foto ? asset('storage/'.$profile->foto) : asset('img/no-image.png'),  
                'nama' => $profile?->nama,
                'no_kk' => $profile?->no_kk,
                'nik' => $profile?->nik,
                'tempat_lahir' => $profile?->tempat_lahir,
                'tanggal_lahir' => $profile?->tanggal_lahir,
                'jenis_kelamin' => $profile?->jenis_kelamin,
                'agama' => $profile?->agama,
                'gol_darah' => $profile?->gol_darah,
                'status_keluarga' => $profile?->status_keluarga,
                'kategori_keluarga' => $profile?->kategori_keluarga,
                'alamat' => $profile?->alamat,
                'domisili' => $profile?->domisili,
                'no_telp' => $profile?->no_telp,
                'no_telp_darurat' => $profile?->no_telp_darurat,
                'email' => $profile?->email,
                'npwp' => $profile?->npwp,
                'no_bpjs_ks' => $profile?->no_bpjs_ks,
                'no_bpjs_kt' => $profile?->no_bpjs_kt,
                'no_rekening' => $profile?->no_rekening,
                'nama_rekening' => $profile?->nama_rekening,
                'nama_bank' => $profile?->nama_bank,
                'nama_ibu_kandung' => $profile?->nama_ibu_kandung,
                'jenjang_pendidikan' => $profile?->jenjang_pendidikan,
                'jurusan_pendidikan' => $profile?->jurusan_pendidikan,
                'jenis_kontrak' => $profile?->jenis_kontrak,
                'status_karyawan' => $status,
                'sisa_cuti_pribadi' => $profile?->sisa_cuti_pribadi,
                'sisa_cuti_bersama' => $profile?->sisa_cuti_bersama,
                'sisa_cuti_tahun_lalu' => $profile?->sisa_cuti_tahun_lalu,
                'expired_date_cuti_tahun_lalu' => $profile?->expired_date_cuti_tahun_lalu,
                'hutang_cuti' => $profile?->hutang_cuti,
                'tanggal_mulai' => $profile?->tanggal_mulai,
                'tanggal_selesai' => $profile?->tanggal_selesai,
                'posisi' => $profile?->posisi()?->pluck('posisis.nama'),
                'grup' => $profile?->grup?->nama,
            ];

            $kontrak = Kontrak::where('karyawan_id', auth()->user()->karyawan->id_karyawan)->orderBy('tanggal_mulai', 'DESC')->first();
            if($kontrak){
                if($kontrak->status == 'DONE'){
                    $badge = '<span class="badge badge-pill badge-success">SEDANG BERJALAN</span>';
                } else {
                    $badge = '<span class="badge badge-pill badge-warning">PROSES PERPANJANGAN</span>';
                } 
                $dataKontrak[] = [
                    'id_kontrak' => $kontrak->id_kontrak,
                    'nama_posisi' => $kontrak->nama_posisi ? $kontrak->nama_posisi : ($kontrak->posisi->nama ? $kontrak->posisi->nama : null),
                    'posisi_id' => $kontrak->posisi_id,
                    'jenis' => $kontrak->jenis,
                    'status' => $kontrak->status,
                    'status_badge' => $badge,
                    'issued_date' => $kontrak->issued_date,
                    'issued_date_text' => Carbon::parse($kontrak->issued_date)->format('d M Y'),
                    'tempat_administrasi' => $kontrak->tempat_administrasi,
                    'durasi' => $kontrak->durasi,
                    'no_surat' => $kontrak->no_surat,
                    'salary' => 'Rp. ' . number_format($kontrak->salary, 0, ',', '.').' ,-',
                    'deskripsi' => $kontrak->deskripsi,
                    'tanggal_mulai' => Carbon::parse($kontrak->tanggal_mulai)->format('d M Y'),
                    'tanggal_selesai' => $kontrak->tanggal_selesai !== null ? Carbon::parse($kontrak->tanggal_selesai)->format('d M Y') : 'Unknown',
                    'attachment' => $kontrak->attachment ? asset('storage/'.$kontrak->attachment) : null
                ];
            }
        } else {
            $dataProfile = [
                'ni_karyawan' => null,
                'foto' => asset('img/no-image.png'),
                'nama' => null,
                'no_kk' => null,
                'nik' => null,
                'tempat_lahir' => null,
                'tanggal_lahir' => null,
                'jenis_kelamin' => null,
                'agama' => null,
                'gol_darah' => null,
                'status_keluarga' => null,
                'kategori_keluarga' => null,
                'alamat' => null,
                'domisili' => null,
                'no_telp' => null,
                'no_telp_darurat' => null,
                'email' => null,
                'npwp' => null,
                'no_bpjs_ks' => null,
                'no_bpjs_kt' => null,
                'no_rekening' => null,
                'nama_rekening' => null,
                'nama_bank' => null,
                'nama_ibu_kandung' => null,
                'jenjang_pendidikan' => null,
                'jurusan_pendidikan' => null,
                'jenis_kontrak' => null,
                'status_karyawan' => null,
                'sisa_cuti_pribadi' => null,
                'sisa_cuti_bersama' => null,
                'sisa_cuti_tahun_lalu' => null,
                'expired_date_cuti_tahun_lalu' => null,
                'hutang_cuti' => null,
                'tanggal_mulai' => null,
                'tanggal_selesai' => null,
                'posisi' => null,
                'grup' => null,
            ];
        }

        $dataPage = [
            'pageTitle' => "SuperApps - Menu",
            'page' => 'menu',
            'profile' => $dataProfile,
            'kontrak' => $dataKontrak,
        ];
        return view('pages.menu.index', $dataPage);
    }

    public function get_approval_lembur_notification(){
        $user = auth()->user();
        $organisasi_id = $user->organisasi_id;
        $approval_lembur = 0;
        if($user->hasRole('personalia')){
            $approval_lembur = Lembure::where(function($query) {
                $query->where(function($query) {
                    $query->where('status', 'WAITING')
                        ->whereNotNull('plan_approved_by')
                        ->whereNull('plan_legalized_by');
                })->orWhere(function($query) {
                    $query->where('status', 'COMPLETED')
                        ->whereNotNull('actual_approved_by')
                        ->whereNull('actual_legalized_by');
                });
            })->where('organisasi_id', $organisasi_id)->count();
        } elseif ($user->karyawan->posisi[0]->jabatan_id == 2 && $user->karyawan->posisi[0]->organisasi_id !== NULL){ 
            $approval_lembur = Lembure::where(function($query) {
                $query->where(function($query) {
                    $query->where('status', 'WAITING')
                        ->whereNull('plan_approved_by');
                })->orWhere(function($query) {
                    $query->where('status', 'COMPLETED')
                        ->whereNull('actual_approved_by');
                });
            })->where('organisasi_id', $organisasi_id)->count();
        } elseif ($user->karyawan->posisi[0]->jabatan_id == 4 || $user->karyawan->posisi[0]->jabatan_id == 3) {
            $posisi = $user->karyawan->posisi;
            $member_posisi_ids = $this->get_member_posisi($posisi);
            $approval_lembur = Lembure::leftJoin('karyawan_posisi', 'lemburs.issued_by', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')->whereIn('posisis.id_posisi', $member_posisi_ids)
            ->where(function($query) {
                $query->where(function($query) {
                    $query->where('status', 'WAITING')
                        ->whereNull('plan_checked_by');
                })->orWhere(function($query) {
                    $query->where('status', 'COMPLETED')
                        ->whereNull('actual_checked_by');
                });
            })->count();
        }

        $lembure = [
            'approval_lembur' => $approval_lembur,
        ];
        
        $html = view('layouts.partials.notification-approval-lembur')->with(compact('lembure'))->render();
        return response()->json(['data' => $html], 200);
    }

    public function get_notification(){
        $notification = [];
        $today = date('Y-m-d');
        $user = auth()->user();
        $tenggang_karyawans = [];

        if($user->hasRole('personalia') || $user->hasRole('super user')){
            $my_cutie = null;
            $tenggang_karyawans = Karyawan::where('status_karyawan', 'AT')
                ->leftJoin('users', 'karyawans.user_id', 'users.id')
                ->where('users.organisasi_id', $user->organisasi_id)
                ->whereRaw('(tanggal_selesai - ?) <= 30', [$today])
                ->selectRaw('*, (tanggal_selesai - ?) as jumlah_hari', [$today])
                ->get();

            $cutie_approval = Cutie::selectRaw('cutis.*, karyawans.nama, (rencana_mulai_cuti - ?) as jumlah_hari',[$today])->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
                ->leftJoin('users', 'karyawans.user_id', 'users.id')
                ->where('users.organisasi_id', $user->organisasi_id)
                ->where('status_dokumen', 'WAITING')
                ->where(function($query) {
                $query->where('status_cuti', '!=', 'CANCELED')
                      ->orWhereNull('status_cuti');
            })
                ->whereNotNull('approved_by')
                ->whereNull('legalized_by')
                ->get();

            $rejected_cuti = [];

        } elseif ($user->hasRole('atasan')){
            $me = auth()->user()->karyawan;
            $posisi = $user->karyawan->posisi;
            $id_posisi_members = $this->get_member_posisi($posisi);

            foreach ($posisi as $ps){
                $index = array_search($ps->id_posisi, $id_posisi_members);
                array_splice($id_posisi_members, $index, 1);
            }

            $members = $id_posisi_members;

            $tenggang_karyawans = Karyawan::where('status_karyawan', 'AT')
                ->whereRaw('(tanggal_selesai - ?) <= 30', [$today])
                ->whereHas('posisi', function($query) use ($members) {
                    $query->whereIn('posisi_id', $members);
                })
                ->selectRaw('*, (tanggal_selesai - ?) as jumlah_hari', [$today])
                ->get();
            //My Cuti
            $my_cutie = Cutie::selectRaw('cutis.*, karyawans.nama, (rencana_mulai_cuti - ?) as jumlah_hari',[$today])->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->where('status_dokumen', 'WAITING')
            ->where(function($query) {
                $query->where('status_cuti', '!=', 'CANCELED')
                      ->orWhereNull('status_cuti');
            })
            ->where('cutis.karyawan_id', $me->id_karyawan)
            ->whereRaw('(rencana_mulai_cuti - ?) <= 7', [$today])
            ->get();

            // Notif Approval
            $cutie_approval = Cutie::selectRaw('cutis.*, karyawans.nama, (rencana_mulai_cuti - ?) as jumlah_hari',[$today])->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->leftJoin('posisis', 'karyawan_posisi.posisi_id', 'posisis.id_posisi')
            ->where('status_dokumen', 'WAITING')
            ->where(function($query) {
                $query->where('status_cuti', '!=', 'CANCELED')
                      ->orWhereNull('status_cuti');
            })
            ->where(function($query) {
                $query->orWhereNull('approved_by')
                        ->orWhereNull('checked1_by')
                        ->orWhereNull('checked2_by');
                })
            ->whereIn('posisis.id_posisi', $members)
            ->whereRaw('(rencana_mulai_cuti - ?) <= 7', [$today])
            ->get();


            $rejected_cuti = Cutie::selectRaw('cutis.*, karyawans.nama')->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->where('status_dokumen', 'REJECTED')
            ->where('cutis.karyawan_id', $me->id_karyawan)
            ->whereRaw('DATE(rejected_at) <= (rencana_mulai_cuti + INTERVAL \'3 days\')')
            ->get()->toArray();

        } else {
            $me = auth()->user()->karyawan;
            $my_cutie = Cutie::selectRaw('cutis.*, karyawans.nama, (rencana_mulai_cuti - ?) as jumlah_hari',[$today])->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->where('status_dokumen', 'WAITING')
            ->where(function($query) {
                $query->where('status_cuti', '!=', 'CANCELED')
                      ->orWhereNull('status_cuti');
            })
            ->where('cutis.karyawan_id', $me->id_karyawan)
            ->whereRaw('(rencana_mulai_cuti - ?) <= 7', [$today])
            ->get();

            $cutie_approval = null;

            $rejected_cuti = Cutie::selectRaw('cutis.*, karyawans.nama')->leftJoin('karyawans', 'cutis.karyawan_id', 'karyawans.id_karyawan')
            ->leftJoin('karyawan_posisi', 'cutis.karyawan_id', 'karyawan_posisi.karyawan_id')
            ->where('status_dokumen', 'REJECTED')
            ->where('cutis.karyawan_id', $me->id_karyawan)
            ->whereRaw('DATE(rejected_at) <= (rencana_mulai_cuti + INTERVAL \'3 days\')')
            ->get()->toArray();

            $tenggang_karyawans = Karyawan::where('status_karyawan', 'AT')->where('id_karyawan', $user->karyawan->id_karyawan)
                ->whereRaw('(tanggal_selesai - ?) <= 30', [$today])
                ->selectRaw('*, (tanggal_selesai - ?) as jumlah_hari', [$today])
                ->get();
        }

        $notification = [
            'count_notif' => $tenggang_karyawans?->count() + $cutie_approval?->count() + count($rejected_cuti) + $my_cutie?->count(),
            'list' => $tenggang_karyawans->toArray(),
            'my_cutie' => $my_cutie ? $my_cutie->toArray() : [],
            'cutie_approval' => $cutie_approval ? $cutie_approval->toArray() : [],
            'rejected_cuti' => $rejected_cuti
        ];

        $html = view('layouts.partials.notification')->with(compact('notification'))->render();
        return response()->json(['data' => $html], 200);
    }

    function get_member_posisi($posisis)
    {
        $data = [];
        foreach ($posisis as $ps) {
            if ($ps->children) {
                $data = array_merge($data, $this->get_member_posisi($ps->children));
            }
            $data[] = $ps->id_posisi;
        }
        return $data;
    }

    public function export_slip_lembur(Request $request)
    {
        $organisasi_id = auth()->user()->organisasi_id;
        $periode = $request->periode_slip;
        $karyawan = auth()->user()->karyawan;
        $id_karyawan = $karyawan->id_karyawan;

        //CREATE EXCEL FILE
        $spreadsheet = new Spreadsheet();

        $fillStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ];
        
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('SLIP LEMBUR');
        $row = 1;
        $headers = [
            'NO',
            'HARI',
            'TANGGAL',
            'JAM MASUK',
            'JAM KELUAR',
            'JAM ISTIRAHAT',
            'JAM KELUAR SETELAH ISTIRAHAT',
            'TOTAL JAM',
            'KONVERSI JAM',
            'UANG MAKAN',
            'JUMLAH'
        ];
        $start = Carbon::createFromFormat('Y-m', $periode)->startOfMonth()->toDateString();
        $end = Carbon::createFromFormat('Y-m', $periode)->endOfMonth()->toDateString();

        $columns = range('A', 'K');
        foreach ($columns as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $lembur_karyawan = DetailLembur::leftJoin('lemburs', 'lemburs.id_lembur', 'detail_lemburs.lembur_id')->where('detail_lemburs.karyawan_id', $id_karyawan)->whereBetween('detail_lemburs.aktual_mulai_lembur', [$start, $end])->whereNotNull('lemburs.actual_legalized_by')
        ->where('lemburs.status', 'COMPLETED')->first();
        $setting_lembur_karyawan = SettingLemburKaryawan::where('karyawan_id', $id_karyawan)->first();
        $pembagi_upah_lembur_harian = SettingLembur::where('organisasi_id', auth()->user()->organisasi_id)->where('setting_name', 'pembagi_upah_lembur_harian')->first()->value;
        $upah_lembur_per_jam_setting = $lembur_karyawan ? $lembur_karyawan->gaji_lembur / $lembur_karyawan->pembagi_upah_lembur : ($setting_lembur_karyawan ? $setting_lembur_karyawan->gaji / $pembagi_upah_lembur_harian : 0);
        // TEXT "SLIP LEMBUR BULAN INI"
        $sheet->mergeCells('A'.$row.':F'.$row+1);
        $sheet->setCellValue('A'.$row, 'SLIP LEMBUR BULAN '.strtoupper(Carbon::createFromFormat('Y-m', $periode)->format('F Y')));
        $sheet->getStyle('A'.$row.':F'.$row+1)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF808080',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        $row += 2;
        $sheet->setCellValue('B'.$row, 'NAMA');
        $sheet->setCellValue('C'.$row, ':');
        $sheet->setCellValue('D'.$row, $karyawan->nama);
        $sheet->setCellValue('B'.$row+1, 'NIK');
        $sheet->setCellValue('C'.$row+1, ':');
        $sheet->setCellValue('D'.$row+1, $karyawan->ni_karyawan);
        $sheet->getStyle('B'.$row.':B'.$row+1)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        $sheet->getStyle('C'.$row.':C'.$row+1)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        $sheet->getStyle('D'.$row.':D'.$row+1)->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
        ]);

        $row += 2;
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . $row, $header);
            $sheet->mergeCells($col . $row.':' . $col . ($row+1));
            $sheet->getStyle($col . $row.':' . $col . ($row+1))->applyFromArray($fillStyle);
            $col++;
        }
        
        $row += 2;
        //LOOPING AWAL SAMPAI AKHIR BULAN
        $total_jam = 0;
        $total_konversi_jam = 0;
        $total_uang_makan = 0;
        $total_spl = 0;
        for($i = 0; $i <= Carbon::parse($start)->diffInDays(Carbon::parse($end)); $i++){
            $date = Carbon::parse($start)->addDays($i)->toDateString();
            $slipLembur = DetailLembur::getSlipLemburPerDepartemen($id_karyawan, $date);
            $upah_lembur_per_jam = $slipLembur ? $slipLembur->gaji_lembur / $slipLembur->pembagi_upah_lembur : $upah_lembur_per_jam_setting;

            if($slipLembur){
                $total_jam += $slipLembur->durasi;
                $total_konversi_jam += $slipLembur->durasi_konversi_lembur;
                $total_uang_makan += $slipLembur->uang_makan;
                $total_spl += $slipLembur->nominal;
                $sheet->setCellValue('A'.$row, $i+1);
                $sheet->setCellValue('B'.$row, Carbon::parse($date)->locale('id')->translatedFormat('l'));

                //JIKA WEEKEND UBAH STYLE CELL
                if(Carbon::parse($date)->isWeekend()){
                    $sheet->getStyle('B'.$row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => 'FFFF0000',
                            ],
                        ],
                        'font' => [
                            'color' => [
                                'argb' => 'FFFFFFFF',
                            ],
                        ],
                    ]);
                }

            $sheet->setCellValue('C'.$row, Carbon::parse($date)->format('d-m-Y'));
            $sheet->setCellValue('D'.$row, Carbon::parse($slipLembur->aktual_mulai_lembur)->format('H:i'));
            $sheet->setCellValue('E'.$row, Carbon::parse($slipLembur->aktual_selesai_lembur)->format('H:i'));
            $sheet->setCellValue('F'.$row, number_format($slipLembur->durasi_istirahat / 100 , 2));
            $sheet->setCellValue('G'.$row, Carbon::parse($slipLembur->aktual_selesai_lembur)->subMinutes($slipLembur->durasi_istirahat)->format('H:i'));
            $sheet->setCellValue('H'.$row, number_format($slipLembur->durasi / 60, 2));
            $sheet->setCellValue('I'.$row, number_format($slipLembur->durasi_konversi_lembur / 60, 2));
            $sheet->setCellValue('J'.$row, $slipLembur->uang_makan);
            $sheet->setCellValue('K'.$row, 'Rp '. number_format($slipLembur->nominal, 0, ',', '.'));

                //STYLE CELL
            $sheet->getStyle('C'.$row)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $sheet->getStyle('J'.$row.':J'.$row)->applyFromArray([
                'font' => [
                    'color' => [
                        'argb' => 'FFFF0000',
                    ],
                ],
            ]);
            $sheet->getStyle('A'.$row.':K'.$row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            } else {
                $sheet->setCellValue('A'.$row, $i+1);
                $sheet->setCellValue('B'.$row, Carbon::parse($date)->locale('id')->translatedFormat('l'));

                //JIKA WEEKEND UBAH STYLE CELL
                if(Carbon::parse($date)->isWeekend()){
                    $sheet->getStyle('B'.$row)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => [
                                'argb' => 'FFFF0000',
                            ],
                        ],
                        'font' => [
                            'color' => [
                                'argb' => 'FFFFFFFF',
                            ],
                        ],
                    ]);
                }

                $sheet->setCellValue('C'.$row, Carbon::parse($date)->format('d-m-Y'));
                $sheet->setCellValue('D'.$row, '-');
                $sheet->setCellValue('E'.$row, '-');
                $sheet->setCellValue('F'.$row, '-');
                $sheet->setCellValue('G'.$row, '-');
                $sheet->setCellValue('H'.$row, '-');
                $sheet->setCellValue('I'.$row, '-');
                $sheet->setCellValue('J'.$row, 0);
                $sheet->setCellValue('K'.$row, 'Rp');
            }

            //STYLE CELL
            $sheet->getStyle('C'.$row)->applyFromArray([
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
            $sheet->getStyle('J'.$row.':K'.$row)->applyFromArray([
                'font' => [
                    'color' => [
                        'argb' => 'FFFF0000',
                    ],
                ],
            ]);
            $sheet->getStyle('A'.$row.':K'.$row)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FF000000'],
                    ],
                ],
            ]);

            $row++;
        }

        $sheet->setCellValue('H'.$row, number_format($total_jam / 60 , 2));    
        $sheet->setCellValue('I'.$row, number_format($total_konversi_jam / 60 , 2));    
        $sheet->setCellValue('J'.$row, 'Rp ' . number_format($total_uang_makan, 0, ',', '.'));    
        $sheet->setCellValue('K'.$row, 'Rp ' . number_format($total_spl, 0, ',', '.'));
        $sheet->setCellValue('J'.$row+1, 'SESUAI SPL');
        $sheet->setCellValue('K'.$row+1, 'Rp ' . number_format($total_spl, 0, ',', '.'));
        $sheet->getStyle('H'.$row.':K'.$row)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ]
        ]);
        $sheet->getStyle('J'.($row+1).':K'.($row+1))
        ->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['argb' => 'FF000000'],
                ],
            ],
            'font' => [
                'bold' => true,
                'size' => 12,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Slip Pembayaran Lembur - '.Carbon::createFromFormat('Y-m', $periode)->format('F Y').'.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit();
    }
}
