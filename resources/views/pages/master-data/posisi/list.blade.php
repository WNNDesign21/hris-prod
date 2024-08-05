<li class="dd-item">
    <div class="dd-handle">{{ $tree->nama }}</div>
    @if ($tree->children->count())
        <ol class="dd-list">
            @foreach ($tree->children as $child)
                @include('pages.master-data.posisi.list', ['tree' => $child])
            @endforeach
        </ol>
    @endif
</li>
