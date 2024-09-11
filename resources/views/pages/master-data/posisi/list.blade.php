<li class="dd-item">
    <div class="dd-handle" style="pointer-events: none">{{ $tree->jabatan->nama . ' - ' . $tree->nama }}</div>
    @if ($tree->children->count())
        <ol class="dd-list">
            @foreach ($tree->children as $child)
                @include('pages.master-data.posisi.list', ['tree' => $child])
            @endforeach
        </ol>
    @endif
</li>
