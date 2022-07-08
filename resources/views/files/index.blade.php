@inject('request', 'Illuminate\Http\Request')
@extends('layouts.master')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Files</h3>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal"
                    data-bs-target="#addFileModal"><i class="fas fa-file-arrow-up"></i> Upload file</button>
                @can('do-admin-stuff')
                <nav class="nav ml-auto">
                    <a class="nav-link" href="{{ request('grid') == 1 ? Request::url().'?grid=1' : Request::url() }}"
                        style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a>
                    <a class="nav-link"
                        href="{{ request('grid') == 1 ? Request::url().'?grid=1&' : Request::url().'?' }}show_deleted=1"
                        style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Trash</a>
                </nav>
                @endcan
            </div>

            {{-- Add File Modal --}}
            <div class="modal fade" id="addFileModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="addFileModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addFileModalLabel">Upload file</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('file.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">

                                <label for="file" class="form-label">File/s</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="basic-addon3"><i class="fas fa-file"></i></span>
                                    <input type="file" name="file[]" class="form-control" id="file"
                                        aria-describedby="basic-addon3" required multiple>
                                </div>

                                <label for="folder" class="form-label">Folder</label>
                                @if($folders->isNotEmpty())
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="fas fa-folder"></i></span>
                                    <select name="folder_id" class="form-select" aria-label="Default select example"
                                        id="folder" required>
                                        @foreach ($folders as $folder)
                                        <option value="{{ $folder->id }}">{{ $folder->folderName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @else
                                <div class="input-group mb-2">
                                    <a href="{{ route('folder.index') }}" class="btn btn-sm btn-success">Create a
                                        folder</a>
                                </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" onclick="displayLoading()" id="upload">
                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"
                                        style="display: none"></span>
                                    <span id="uploadtxt">Upload</span></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <hr>
            <div class="mt-1">
                <nav class="nav ml-auto">
                    <a class="nav-link"
                        href="{{ request('show_deleted') == 1 ? Request::url().'?show_deleted=1' : Request::url() }}"
                        style="{{ request('grid') == 1 ? '' : 'font-weight: 700' }}"><i class="fas fa-list"></i> List
                        view</a>
                    <a class="nav-link"
                        href="{{ request('show_deleted') == 1 ? Request::url().'?show_deleted=1&' : Request::url().'?' }}grid=1"
                        style="{{ request('grid') == 1 ? 'font-weight: 700' : '' }}"><i class="fas fa-th"></i> Grid
                        view</a>
                </nav>
            </div>
            @if (request('grid') == 1)
            <div class="row mt-2 ml-3 justify-content-center">
                @forelse ($files as $file)
                <div class="card mx-1 mt-2" style="width: 12rem;" data-toggle="popover" data-trigger="hover"
                    title="{{ $file->fileName }}">
                    @if (in_array(pathinfo(storage_path($file->filePath), PATHINFO_EXTENSION), $image))
                    <img src="{{ Thumbnail::src('/'.$file->filePath, 'public')->smartcrop(200, 200)->url() }}"
                        class="card-img-top mt-2">
                    @else
                    <img src="https://www.pngall.com/wp-content/uploads/2018/05/Files-PNG-File.png"
                        class="card-img-top mt-2" alt="...">
                    @endif

                    <div class="card-body">
                        <div class="col-auto px-0">
                            <p class="card-text text-truncate">{{ $file->fileName }}</p>
                        </div>
                        <h6 class="card-subtitle text-muted">{{ $file->user->full_name() }}</h6>
                        <div class="dropdown d-flex justify-content-end">
                            <i class="fas fa-ellipsis-vertical" type="button" id="dropdownMenu{{ $file->id }}"
                                data-bs-toggle="dropdown" aria-expanded="false">
                            </i>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenu{{ $file->id }}">
                                <li><a href="{{ route('file.download', $file->id) }}" class="dropdown-item"
                                        type="button">Download</a></li>
                                @if (request('show_deleted') != 1)
                                <li><a href="#" class="dropdown-item" data-bs-toggle="modal"
                                        data-bs-target="#renameModal{{ $file->id }}">Rename</a></li>
                                @endif
                                @can('do-admin-stuff')
                                @if (request('show_deleted') == 1)
                                <li><a href="{{ route('file.recover', $file->id) }}" class="dropdown-item"
                                        type="button">Restore</a></li>
                                @endif
                                @endcan
                                @if(!request('show_deleted') == 1)
                                <li><button class="dropdown-item" type="submit" data-bs-toggle="modal"
                                        data-bs-target="#removeFileModal"
                                        data-url="{{route('file.destroy', $file->id)}}"
                                        id="btn-delete-file">Delete</button>
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
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{route('file.destroy', $file->id)}}" method="POST"
                                            id="removeFileModalForm">
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
            </div>
            @else
            <table class="table table-bordered hover compact" id="fileIndexTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Uploader</th>
                        @if(request('show_deleted') == 1)
                        <th>Date deleted</th>
                        @endif
                        <th>Date uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($files as $file)
                    <tr>
                        <td>{{ $file->fileName }}</td>
                        <td>{{ $file->user == NULL ? 'Deleted user' : $file->user->first_name.'
                            '.$file->user->last_name
                            }}</td>
                        @if(request('show_deleted') == 1)
                        <td>{{ $file->deleted_at->format('M j, Y \a\t g:i:s A') }}</td>
                        @endif
                        <td>{{ $file->created_at->format('M j, Y \a\t g:i:s A') }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                @can('do-admin-stuff')
                                @if (request('show_deleted') == 1)
                                <a href="{{ route('file.recover', $file->id) }}"
                                    class="btn btn-sm btn-success mr-1">Restore</a>
                                @endif
                                @endcan
                                <a href="{{ route('file.download', $file->id) }}" download
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                                @if(!request('show_deleted') == 1)
                                <button href="#" class="btn btn-sm btn-success ml-1" data-bs-toggle="modal"
                                    data-bs-target="#renameModal{{ $file->id }}"><i class="fas fa-edit"></i></button>
                                @include('files.modal._rename')
                                <button type="submit" class="btn btn-danger btn-sm mx-1" data-bs-toggle="modal"
                                    data-bs-target="#removeFileModal" data-url="{{route('file.destroy', $file->id)}}"
                                    id="btn-delete-file">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <a href="#" class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#shareModal{{ $file->id }}">
                                    <i class="fas fa-share-nodes"></i>
                                </a>
                                @include('files.modal._share')
                                @endif

                                {{-- Delete Confirm Modal --}}
                                <div class="modal fade" id="removeFileModal" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="removeFileLabel">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{route('file.destroy', $file->id)}}" method="POST"
                                                id="removeFileModalForm">
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
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            <div class="mt-4 d-flex justify-content-end">
                {{ $files->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready( function () {
            $('#fileIndexTable').DataTable({
                order: [[2, 'desc']],
                paging: false,
                info: false,
            });
        });
</script>

<script>
    $(document).on('click', '#btn-delete-file', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        $('#removeFileModalForm').attr('action', url);
    });
</script>
@endsection