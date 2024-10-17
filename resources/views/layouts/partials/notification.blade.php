<a href="#" class="btn btn-light dropdown-toggle position-relative" data-bs-toggle="dropdown" title="Notifications">
    <i class="icon-Notifications"><span class="path1"></span><span class="path2"></span></i>
    @if ($notification['count_notif'] > 0)
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="font-size: 1rem;z-index:2;">
            {{ $notification['count_notif'] }}
        </span>
    @endif
</a>
<ul class="dropdown-menu animated bounceIn" style="min-width:300px;max-width:350px;">
    <li class="header">
        <div class="p-20">
            <div class="flexbox">
                <div>
                    <h4 class="mb-0 mt-0">Notifications</h4>
                </div>
            </div>
        </div>
    </li>
    <li>
        <!-- inner menu: contains the actual data -->
        <ul class="menu sm-scroll">
            @if (!empty($notification['list']))
                @foreach ($notification['list'] as $list)
                    <li>
                        @if (auth()->user()->hasRole('personalia') || auth()->user()->hasRole('super user') || auth()->user()->hasRole('atasan'))
                            <a href="{{ route('master-data.kontrak') }}" style="white-space: normal;">
                                <i class="fa fa-users text-danger"></i> <strong>{{ $list['nama'] }}</strong>
                                memiliki
                                sisa
                                <strong>{{ $list['jumlah_hari'] }} Hari</strong> sebelum masa
                                <strong>HABIS KONTRAK</strong>.
                            </a>
                        @else
                            <a href="#" style="pointer-events: none; cursor: default;white-space: normal;">
                                <i class="fa fa-user text-danger"></i>Anda memiliki sisa
                                <strong>{{ $list['jumlah_hari'] }} Hari</strong> sebelum masa
                                <strong>HABIS KONTRAK</strong>, segera hubungi atasan anda.
                            </a>
                        @endif
                    </li>
                @endforeach
            @endif
            @if (!empty($notification['cutie_approval']))
                @foreach ($notification['cutie_approval'] as $cutie_approval)
                    <li>
                        @if (auth()->user()->hasRole('personalia') || auth()->user()->hasRole('super user'))
                            <a href="{{ route('cutie.personalia-cuti') }}" style="white-space: normal;">
                                <i class="fa fa-warning text-warning"></i>Pengajuan
                                Cuti
                                <strong>{{ $cutie_approval['nama'] }}</strong>
                                menunggu Legalized oleh HRD
                                <br>
                                <strong>{{ $cutie_approval['jumlah_hari'] }} Hari</strong>
                                sebelum
                                otomatis
                                <strong>REJECTED</strong>.
                            </a>
                        @elseif (auth()->user()->hasRole('atasan'))
                            <a href="{{ route('cutie.member-cuti') }}" style="white-space: normal;">
                                <i class="fa fa-warning text-warning"></i>Pengajuan
                                Cuti
                                <strong>{{ $cutie_approval['nama'] }}</strong>
                                menunggu Check / Approve oleh Atasan
                                <br>
                                <strong>{{ $cutie_approval['jumlah_hari'] }} Hari</strong>
                                sebelum
                                otomatis
                                <strong>REJECTED</strong>.
                            </a>
                        @else
                            <a href="{{ route('cutie.pengajuan-cuti') }}" style="white-space: normal;">
                                <i class="fa fa-warning text-warning"></i>Pengajuan
                                Cuti
                                {{ $cutie_approval['jenis_cuti'] }} dengan durasi
                                {{ $cutie_approval['durasi_cuti'] }} Hari, masih menunggu
                                persetujuan,<br> segera follow-up atasan & HRD untuk
                                menindaklanjuti!
                                <br>
                                <strong>{{ $cutie_approval['jumlah_hari'] }} Hari</strong>
                                sebelum
                                otomatis
                                <strong>REJECTED</strong>.
                            </a>
                        @endif
                    </li>
                @endforeach
            @endif
            @if (!empty($notification['rejected_cuti']))
                @foreach ($notification['rejected_cuti'] as $rejected_cuti)
                    <li>
                        <a href="{{ route('cutie.pengajuan-cuti') }}" style="white-space: normal;">
                            <i class="fas fa-circle-xmark text-danger"></i></i>Pengajuan
                            Cuti
                            {{ $rejected_cuti['jenis_cuti'] }} dengan durasi
                            {{ $cutie_approval['durasi_cuti'] }} Hari
                            <strong>DITOLAK</strong> pada
                            {{ \Carbon\Carbon::parse($rejected_cuti['rejected_at'])->format('Y-m-d H:i:s') }}
                            <br>
                            Pesan ini akan otomatis terhapus H+3 sejak Rencana Mulai Cuti
                        </a>
                    </li>
                @endforeach
            @endif
            @if (empty($notification['list']) && empty($notification['cutie_approval']) && empty($notification['rejected_cuti']))
                <li>
                    <a href="#" class="text-center">
                        Everything is just Fine 👌
                    </a>
                </li>
            @endif
        </ul>
    </li>
</ul>
