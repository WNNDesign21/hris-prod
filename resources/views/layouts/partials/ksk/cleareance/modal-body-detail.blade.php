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
                <th>Keterangan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detail as $item)
                <tr>
                    <td>
                        @if ($item->type == 'AL')
                            <p><strong>Atasan Langsung</strong></p>
                        @elseif ($item->type == 'IT')
                            <p><strong>Dept.IT</strong></p>
                        @elseif ($item->type == 'FAT')
                            <p><strong>Dept.Finance</strong></p>
                        @elseif ($item->type == 'GA')
                            <p><strong>Dept.GA</strong></p>
                        @elseif ($item->type == 'HR')
                            <p><strong>Dept.HR</strong></p>
                        @endif
                        <select name="confirmed_by_id" id="confirmed_by_id{{ $item->type }}" class="form-control"
                            style="width: 100%" required>
                            @if ($item->confirmed_by_id)
                                <option value="">TIDAK DIPERLUKAN</option>
                                <option value="{{ $item->confirmed_by_id }}" selected>
                                    {{ $item->karyawan->nama }}</option>
                            @else
                                <option value="">TIDAK DIPERLUKAN</option>
                            @endif
                        </select>
                    </td>
                    <td>
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
                    <td id="keteranganText{{ $item->type }}">{{ $item->keterangan }}</td>
                    <td id="statusText{{ $item->type }}">
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
                    <td>
                        <button class="btn btn-warning btnRollback"
                            data-id-cleareance-detail="{{ $item->id_cleareance_detail }}"
                            data-type="{{ $item->type }}">
                            <i class="fas fa-undo"></i> Rollback
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
