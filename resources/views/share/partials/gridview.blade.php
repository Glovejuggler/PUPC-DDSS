@if($files->isNotEmpty())
<h5 class="fw-bold my-2">Files</h5>
@endif
@forelse ($files as $file)
<div class="card mx-2 my-2 col-md-3 col-lg-2" data-toggle="popover" data-trigger="hover" title="{{ $file->fileName }}">
    @if (in_array(pathinfo(storage_path($file->filePath), PATHINFO_EXTENSION), $image))
    <img src="{{ Thumbnail::src('/'.$file->filePath, 'public')->crop(200, 200)->url() }}" class="card-img-top mt-2">
    @else
    <img src="https://www.pngall.com/wp-content/uploads/2018/05/Files-PNG-File.png" class="card-img-top mt-2" alt="...">
    @endif

    <div class="card-body">
        <div class="col-auto px-0">
            <p class="card-text text-truncate">{{ $file->fileName }}</p>
        </div>
        <h6 class="card-subtitle text-muted">{{ $file->user->full_name() }}</h6>
        <div class="dropdown d-flex justify-content-end">
            <i class="fas fa-ellipsis" type="button" id="dropdownMenu{{ $file->id }}" data-bs-toggle="dropdown"
                aria-expanded="false">
            </i>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $file->id }}">
                <li><a href="{{ route('file.download', $file->id) }}" class="dropdown-item" type="button">Download</a>
                </li>
            </ul>
        </div>
    </div>
</div>
@empty
<div class="d-flex justify-content-center">
    <h5 class="text-muted">No files to display</h5>
</div>
@endforelse
<div class="mt-4 d-flex justify-content-end">
    {{ $files->withQueryString()->links() }}
</div>