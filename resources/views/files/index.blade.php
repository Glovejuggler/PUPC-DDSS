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
                    <a class="nav-link" href="{{ route('file.index') }}"
                        style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a>
                    <a class="nav-link" href="{{ route('file.index') }}?show_deleted=1"
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

            <table class="table table-bordered datatable dt-select" id="myTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Uploader</th>
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
                                <button type="submit" class="btn btn-danger btn-sm mx-1" data-bs-toggle="modal"
                                    data-bs-target="#removeFileModal" data-url="{{route('file.destroy', $file->id)}}"
                                    id="btn-delete-file">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <a href="{{ route('share.file', $file->id) }}" class="btn btn-secondary btn-sm">
                                    <i class="fas fa-share-nodes"></i>
                                </a>
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

                                {{-- Share File Modal --}}
                                <div class="modal fade" id="shareFileModal" data-bs-backdrop="static"
                                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="shareFileModalLabel"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="shareFileModalLabel">Share file</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-info" role="alert">
                                                    This feature is not yet working
                                                </div>
                                                @foreach ($roles as $role)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        value="{{ $role->id }}" id="defaultCheck1">
                                                    <label class="form-check-label" for="defaultCheck1">
                                                        {{ $role->roleName }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                                <button type="submit" class="btn btn-primary" onclick="displayLoading()"
                                                    id="upload">
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true" style="display: none"></span>
                                                    <span id="uploadtxt">Share</span></button>
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
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).on('click', '#btn-delete-file', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        $('#removeFileModalForm').attr('action', url);
    });

    function displayLoading(){
        e.preventDefault();
        document.getElementById('loading').style.display = "inline-block"
        document.getElementById('upload').disabled = true;
        document.getElementById('uploadtxt').innerHTML = 'Uploading';
    }
</script>
@endsection