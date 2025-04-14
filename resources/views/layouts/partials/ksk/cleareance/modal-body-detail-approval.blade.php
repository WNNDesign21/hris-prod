<div class="row">
    <div class="col-12 d-flex justify-content-center text-center">
        <h1 class="box-title">Exit Employee Clearance<br><small>ID : {{ $header->id_cleareance }}</small></h1>
    </div>
    <p><strong>Nama :</strong> {{ $header->karyawan->nama }}</p>
    <p><strong>Divisi :</strong> {{ $header->nama_divisi }}</p>
    <p><strong>Departemen :</strong> {{ $header->nama_departemen }}</p>
    <p><strong>Jabatan :</strong> {{ $header->nama_jabatan }}</p>
    <p><strong>Posisi :</strong> {{ $header->nama_posisi }}</p>
    <p><strong>Tanggal Akhir Bekerja :</strong>
        {{ \Carbon\Carbon::parse($header->tanggal_akhir_bekerja)->format('d F Y') }}</p>
    <p><strong>Status :</strong>
        @if ($header->status == 'Y')
            <span class="badge badge-success">COMPLETED</span>
        @else
            <span class="badge badge-warning">WAITING</span>
        @endif
    </p>
</div>
<div class="table-responsive">
    <table id="detail-cleareance-table" class="table border-primary b-1 table-bordered" style="width:100%">
        <thead class="bg-primary">
            <tr>
                <th>Departemen</th>
                <th>Deskripsi</th>
                <th>Status</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $item)
                <tr>
                    <td style="width: 20%">
                        @if ($item->type == 'AL')
                            <p><strong>Atasan Langsung</strong><br>
                            @elseif ($item->type == 'IT')
                            <p><strong>Dept.IT</strong><br>
                            @elseif ($item->type == 'FAT')
                            <p><strong>Dept.Finance</strong><br>
                            @elseif ($item->type == 'GA')
                            <p><strong>Dept.GA</strong><br>
                            @elseif ($item->type == 'HR')
                            <p><strong>Dept.HR</strong><br>
                        @endif
                        {{ $item->karyawan->nama }}</p>
                    </td>
                    <td style="width: 20%">
                        @if ($item->type == 'AL')
                            <p><strong>Deskripsi Atasan Langsung</strong></p>
                        @elseif ($item->type == 'HR')
                            <p><strong>Deskripsi HR</strong></p>
                        @elseif ($item->type == 'IT')
                            <p><strong>Deskripsi IT</strong></p>
                        @elseif ($item->type == 'GA')
                            <p><strong>Deskripsi GA</strong></p>
                        @elseif ($item->type == 'FAT')
                            <p><strong>Deskripsi FAT</strong></p>
                        @endif
                    </td>
                    <td style="width: 20%" id="statusText{{ $item->type }}">
                        @if ($item->is_clear == 'Y')
                            <p>
                                ✅{{ $item->confirmed_by }}<br>
                                <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->confirmed_at)->format('d F Y H:i') }}</span>
                            </p>
                        @else
                            <p>
                                @if ($item->confirmed_by)
                                    ❌{{ $item->confirmed_by }}<br>
                                    <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $item->confirmed_at)->format('d F Y H:i') }}</span>
                                @else
                                    <p>⏳ Waiting</p>
                                @endif
                            </p>
                        @endif
                    </td>
                    <td style="width: 20%">
                        @if ($item->confirmed_by_id == auth()->user()->karyawan->id_karyawan)
                            @if (!$item->confirmed_by)
                                <textarea name="keteranganApproval" id="keteranganApproval{{ $item->type }}" cols="30" rows="3"
                                    class="form-control"></textarea>
                            @else
                                {{ $item->keterangan }}
                            @endif
                        @else
                            {{ $item->keterangan }}
                        @endif
                    </td>
                    <td style="width: 20%">
                        @if ($item->confirmed_by_id == auth()->user()->karyawan->id_karyawan)
                            @if (!$item->confirmed_by)
                                <button class="btn btn-success btnKonfirmasi"
                                    data-id-cleareance-detail="{{ $item->id_cleareance_detail }}"
                                    data-type="{{ $item->type }}">
                                    <i class="fas fa-check"></i> Konfirmasi
                                </button>
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
