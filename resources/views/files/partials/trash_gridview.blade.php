@if($folders->isNotEmpty())
<h5 class="fw-bold">Folders</h5>
@forelse ($folders as $folder)
<div class="card col-lg-2 col-md-3 px-0 py-0 mx-1 mb-2">
    <div class="card-body">
        <div class="d-flex justify-content-between">
            <span>
                <i class="fas fa-folder text-yellow"></i> {{ $folder->folderName }}
            </span>
            <span>
                <i class="fas fa-ellipsis-vertical" type="button" id="dropdownMenu{{ $folder->id }}"
                    data-bs-toggle="dropdown" aria-expanded="false"></i>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $folder->id }}">
                    </li>
                    <li><a href="{{ route('folder.recover', $folder->id) }}" class="dropdown-item">Restore</a>
                    </li>
                </ul>
            </span>
        </div>
    </div>
</div>
@empty
<h6>No folders to display</h6>
@endforelse
@endif

@if($files->isNotEmpty())
<h5 class=" fw-bold">Files</h5>
@endif
@forelse ($files as $file)
<div class="card mx-1 mt-2 mt-2 col-md-3 col-lg-2" data-toggle="popover" data-trigger="hover"
    title="{{ $file->fileName }}">
    <img src="{{ DDSS::file_thumb($file) }}" class="card-img-top mt-2">

    <div class="card-body">
        <div class="col-auto px-0">
            <p class="card-text text-truncate">{{ $file->fileName }}</p>
        </div>
        <h6 class="card-subtitle text-muted">{{ $file->user->full_name() }}</h6>
        <div class="dropdown d-flex justify-content-end">
            <i class="fas fa-ellipsis-vertical" type="button" id="dropdownMenu{{ $file->id }}" data-bs-toggle="dropdown"
                aria-expanded="false">
            </i>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $file->id }}">
                <li><a href="{{ route('file.download', $file->id) }}" class="dropdown-item" type="button">Download</a>
                </li>
                <li><a href="{{ route('file.recover', $file->id) }}" class="dropdown-item" type="button">Restore</a>
                </li>
            </ul>
        </div>
    </div>
</div>
@empty
@if($folders->isEmpty())
<div class="d-flex justify-content-center">
    <h5 class="text-muted">No files to display</h5>
</div>
@endif
@endforelse
<div class="mt-4 d-flex justify-content-end">
    {{ $files->withQueryString()->links() }}
</div>