@if ($datas->isNotEmpty())
    @foreach ($datas as $history)
        <div class="col-6 col-lg-3">
            <p><strong>{{ $history->changed_by }}</strong><br>
                @if ($history->status_ksk_after == 'PPJ')
                    <span class="badge badge-success">Perpanjang (PKWT)</span>
                @elseif ($history->status_ksk_after == 'PPJMG')
                    <span class="badge badge-success">Perpanjang (MAGANG)</span>
                @elseif ($history->status_ksk_after == 'TTP')
                    <span class="badge badge-primary">Karyawan Tetap</span>
                @elseif ($history->status_ksk_after == 'PHK')
                    <span class="badge badge-danger">PHK</span>
                @endif
                <br>
                {{ $history->durasi_after }} Bulan<br>

                {{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $history->created_at)->format('d F Y H:i') }}
                WIB <br>
                Alasan : {{ $history->reason }}<br>
            </p>
        </div>
    @endforeach
@endif
