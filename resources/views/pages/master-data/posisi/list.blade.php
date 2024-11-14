<li class="dd-item">
    <div class="dd-handle" style="cursor: pointer;" data-id="{{ $tree->id_posisi }}" data-posisi-nama="{{ $tree->nama }}"
        data-parent-id="{{ $tree->parent_id }}" data-jabatan-id="{{ $tree->jabatan_id }}"
        data-organisasi-id="{{ $tree->organisasi_id }}" data-divisi-id="{{ $tree->divisi_id }}"
        data-departemen-id="{{ $tree->departemen_id }}" data-seksi-id="{{ $tree->seksi_id }}">
        {{ $tree->jabatan->nama . ' - ' . $tree->nama }} <i class="fas fa-user-edit"></i></div>
    @if ($tree->children->count())
        <ol class="dd-list">
            @foreach ($tree->children as $child)
                @include('pages.master-data.posisi.list', ['tree' => $child])
            @endforeach
        </ol>
    @endif
</li>
