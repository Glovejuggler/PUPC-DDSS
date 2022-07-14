@forelse ($files as $file)
<div class="card mx-1 mt-2" style="width: 12rem;" data-toggle="popover" data-trigger="hover"
    title="{{ $file->fileName }}">
    @if (in_array(pathinfo(storage_path($file->filePath), PATHINFO_EXTENSION), $image))
    <img src="{{ Thumbnail::src('/'.$file->filePath, 'public')->smartcrop(200, 200)->url() }}"
        class="card-img-top mt-2">
    @else
    <img src="https://www.pngall.com/wp-content/uploads/2018/05/Files-PNG-File.png" class="card-img-top mt-2" alt="...">
    @endif

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
                @if (request('show_deleted') != 1)
                <li><a href="#" class="dropdown-item" data-bs-toggle="modal"
                        data-bs-target="#renameModal{{ $file->id }}">Rename</a></li>
                @endif
                @can('do-admin-stuff')
                @if (request('show_deleted') == 1)
                <li><a href="{{ route('file.recover', $file->id) }}" class="dropdown-item" type="button">Restore</a>
                </li>
                @endif
                @endcan
                @if(!request('show_deleted') == 1)
                <li><button class="dropdown-item" type="submit" data-bs-toggle="modal" data-bs-target="#removeFileModal"
                        data-url="{{route('file.destroy', $file->id)}}" id="btn-delete-file">Delete</button>
                </li>
                <li><a href="#" class="dropdown-item" data-bs-toggle="modal"
                        data-bs-target="#shareModal{{ $file->id }}">Share</a></li>
                @endif
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
<div class="d-flex justify-content-center">
    <h5 class="text-muted">No files to display</h5>
</div>
@endforelse
<div class="mt-4 d-flex justify-content-end">
    {{ $files->withQueryString()->links() }}
</div>