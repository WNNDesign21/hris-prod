@if ($attachments)
    @foreach ($attachments as $index => $attach)
        <a id="attachmentPreview_{{ $index }}" href="{{ asset('storage/ ' . $attach->path) }}"
            data-title="Attachment Ke-{{ $index }}" target="_blank">
            <img src="{{ asset('img/pdf-img.png') }}" alt="Attachment" style="width: 3.5rem;height: 3.5rem;" class="p-0">
        </a>
    @endforeach
@else
    <p>No Attachment Uploaded</p>
@endif
