@if($folders->isNotEmpty())
<h5 class="fw-bold mb-2">Folders</h5>
@forelse ($folders as $folder)
<div class="card col-lg-2 col-md-12 my-2 mx-2" id="folder{{ $folder->id }}" data-toggle="popover" data-trigger="hover"
    title="{{ $folder->folderName }}">
    <div class="card-body px-0 py-0">
        <div class="d-flex justify-content-between">
            <a href="{{ route('file.index', $folder->id).'?grid=1' }}"
                class="text-decoration-none text-dark text-truncate py-3 pl-1">
                <i class="fas fa-folder text-yellow"></i> {{ $folder->folderName }}
            </a>
            <i class="fas fa-ellipsis-vertical my-auto mr-2 ml-4" type="button" id="dropdownMenu{{ $folder->id }}"
                data-bs-toggle="dropdown" aria-expanded="false"></i>
            <ul class="dropdown-menu dropdown-menu-end drop{{ $folder->id }}"
                aria-labelledby="#dropdownMenu{{ $folder->id }}">
                </li>
                <li><a href="#" class="dropdown-item" data-bs-toggle="modal"
                        data-bs-target="#renameFolderModal{{ $folder->id }}">Rename</a>
                </li>
                <li><button class="dropdown-item" type="submit" data-bs-toggle="modal"
                        data-bs-target="#removeFolderModal" data-url="{{route('folder.destroy', $folder->id)}}"
                        id="btn-delete-folder">Delete</button>
                </li>
                <li><a href="#" class="dropdown-item" data-bs-toggle="modal"
                        data-bs-target="#shareFolderModal{{ $folder->id }}">Share</a>
                </li>
            </ul>
        </div>
    </div>
</div>
@include('folders.modal._rename')
@include('folders.modal._share')

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="removeFolderModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeFolderLabel">Confirmation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{route('folder.destroy', $folder->id)}}" method="POST" id="removeFolderModalForm">
                @method('DELETE')
                @csrf
                <div class="modal-body">
                    Are you sure you want to delete this folder?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@empty
<h6>No folders to display</h6>
@endforelse
@endif

@if($files->isNotEmpty())
<h5 class="fw-bold my-2">Files</h5>
@endif
@forelse ($files as $file)
<div class="card mx-2 my-2 col-md-3 col-lg-2" data-toggle="popover" data-trigger="hover" title="{{ $file->fileName }}">

    <img src="{{ DDSS::file_thumb($file) }}" class="card-img-top mt-2">

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
                <li><a href="#" class="dropdown-item" data-bs-toggle="modal"
                        data-bs-target="#renameModal{{ $file->id }}">Rename</a></li>
                <li><button class="dropdown-item" type="submit" data-bs-toggle="modal" data-bs-target="#removeFileModal"
                        data-url="{{route('file.destroy', $file->id)}}" id="btn-delete-file">Delete</button>
                </li>
                <li><a href="#" class="dropdown-item" data-bs-toggle="modal"
                        data-bs-target="#shareModal{{ $file->id }}">Share</a></li>
            </ul>

            {{-- Delete Confirm Modal --}}
            <div class=" modal fade" id="removeFileModal" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="removeFileLabel">Confirmation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{route('file.destroy', $file->id)}}" method="POST" id="removeFileModalForm">
                            @method('DELETE')
                            @csrf
                            <div class="modal-body">
                                Are you sure you want to delete this file?
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-secondary"
                                    data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@include('files.modal._rename')
@include('files.modal._share')
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