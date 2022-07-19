@inject('request', 'Illuminate\Http\Request')
@extends('layouts.master')

@section('css')
<style>
    .table-row-pointer:hover {
        cursor: default;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/filepond/4.30.4/filepond.min.css">
@endsection

@section('content')
<div class="container">
    <form action="{{ route('file.store') }}" method="post">
        @csrf
        <input type="file" name="file[]" required multiple />
        <p class="help-block">{{ $errors->first('file.*') }}</p>
        <label for="folder" class="form-label">Folder</label>
        @if($folders->isNotEmpty())
        <div class="input-group mb-2">
            <span class="input-group-text"><i class="fas fa-folder"></i></span>
            <select name="folder_id" class="form-select" aria-label="Default select example" id="folder" required>
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
        <button type="submit" class="btn btn-sm btn-success">Upload</button>
    </form>
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Files</h3>
        </div>
        <div class="card-body overflow-auto">
            <div class="d-flex justify-content-end">
                <a href="{{ request('grid') == 1 ? Request::url() : Request::url().'?grid=1' }}"
                    class="text-dark text-decoration-none"><i
                        class="fas {{ request('grid') == 1 ? 'fa-list' : 'fa-th' }}"></i> {{
                    request('grid') == 1 ?
                    'List view' : 'Grid view' }}</a>
            </div>
            <hr>
            @if (request('grid') == 1)
            <div class="row d-flex justify-content-end px-3">
                <div class="input-group my-2" style="width: 25%;">
                    <input type="text" class="form-control" placeholder="Search" aria-label="Search"
                        aria-describedby="button-addon2" name="search" id="search">
                </div>
            </div>
            <div class="row mt-2 px-3 justify-content-center" id="gridView">
                @include('files.partials.gridview')
            </div>
            @else
            <table class="hover compact" id="fileIndexTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Uploader</th>
                        <th>Date uploaded</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($files as $file)
                    <tr class="table-row-pointer">
                        <td>{{ $file->fileName }}</td>
                        <td>{{ $file->user == NULL ? 'Deleted user' : $file->user->first_name.'
                            '.$file->user->last_name
                            }}</td>
                        <td>{{ $file->created_at->format('M j, Y \a\t g:i:s A') }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('file.download', $file->id) }}" download
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-secondary ml-1"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis"></i>
                                    </button>
                                    <ul class="dropdown-menu" id="dropdownmenu{{ $file->id }}">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#renameModal{{ $file->id }}"><i class="fas fa-edit"></i>
                                                Rename</a>
                                        </li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#shareModal{{ $file->id }}"><i
                                                    class="fas fa-share-nodes"></i>
                                                Share</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#removeFileModal"
                                                data-url="{{route('file.destroy', $file->id)}}" id="btn-delete-file"><i
                                                    class="fas fa-trash"></i> Delete</a>
                                        </li>
                                    </ul>
                                    @include('files.modal._rename')
                                    @include('files.modal._share')
                                </div>

                                {{-- Delete Confirm Modal --}}
                                <div class="modal fade text-dark" id="removeFileModal" aria-hidden="true">
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
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready( function () {
        var table = $('#fileIndexTable').DataTable({
            columnDefs: [
            { orderable: false, targets: 3 }
            ],
            order: [[2, 'desc']],
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

<script>
    $(document).ready(function(){
        $('#search').on('keyup', function(){
            event.preventDefault();
            var search = $('#search').val();
            $.ajax({
                type: 'get',
                url: '{{ route('file.search') }}',
                data: {'search':search},
                success: function(data) {
                    $('#gridView').html(data);
                },
            })
        });
    });
</script>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>

<script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>

<script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>

<script>
    FilePond.setOptions({
        server: {
            url: "{{ config('filepond.server.url') }}",
            headers: {
                'X-CSRF-TOKEN': "{{ @csrf_token() }}",
            }
        }
    });
    FilePond.create(document.querySelector('input[name="file[]"]'), {chunkUploads: true});
</script>
@endsection