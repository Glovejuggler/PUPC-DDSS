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
    <form action=" {{ route('file.store') }}" method="post">
        @csrf
        <input type="file" name="file[]" required multiple />
        <p class="help-block">{{ $errors->first('file.*') }}</p>
        <input type="number" name="folder_id" id="folder_id" value="{{ $current_folder?->id }}" hidden>
        <button type="submit" id="uploadButton" class="btn btn-sm btn-success d-none">Upload</button>
    </form>
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">
                {{ $current_folder ? $current_folder->folderName : 'Root' }}
            </h3>
        </div>
        <div class="card-body overflow-hidden">
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal"
                    data-bs-target="#addFolderModal"><i class="fas fa-folder-plus"></i> Create folder</button>
                <a href="{{ request('grid') == 1 ? Request::url() : Request::url().'?grid=1' }}"
                    class="text-dark text-decoration-none"><i
                        class="fas {{ request('grid') == 1 ? 'fa-list' : 'fa-th' }}"></i> {{
                    request('grid') == 1 ?
                    'List view' : 'Grid view' }}</a>
            </div>

            {{-- Add Folder Modal --}}
            <div class="modal fade" id="addFolderModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="addFolderModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addFolderModalLabel">Add new folder</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('folder.store') }}" method="post">
                            @csrf
                            <div class="modal-body">
                                <label for="folderName" class="form-label">Folder name</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="basic-addon3"><i
                                            class="fas fa-folder"></i></span>
                                    <input type="text" name="folderName" class="form-control" id="folderName"
                                        aria-describedby="basic-addon3" required>
                                    <input type="number" name="parent_folder_id" id="parent_folder_id"
                                        value="{{ $current_folder?->id }}" hidden>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <hr>
            @if ($current_folder)
            <a href="{{ request('grid') == 1 ? route('file.index', $current_folder->parent_folder_id).'?grid=1' : route('file.index', $current_folder->parent_folder_id) }}"
                class="btn btn-sm btn-outline-secondary mb-4"><i class="fas fa-level-up"></i> Back</a>
            @endif
            @if (request('grid') == 1)
            <div class="row d-flex justify-content-end px-3">
                <div class="input-group my-2" style="width: 25%;">
                    <input type="text" class="form-control" placeholder="Search" aria-label="Search"
                        aria-describedby="button-addon2" name="search" id="search">
                </div>
            </div>
            <div class="row mt-2 px-2" id="gridView">
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
                    @forelse ($folders as $folder)
                    <tr class="table-row-pointer">
                        <td><a href="{{ route('file.index', $folder->id) }}" class="text-decoration-none text-dark"><i
                                    class="fas fa-folder text-yellow"></i> {{
                                $folder->folderName }}</a></td>
                        <td>{{ $folder->user == NULL ? 'Deleted user' : $folder->user->first_name.'
                            '.$folder->user->last_name
                            }}</td>
                        <td>{{ $folder->created_at?->format('M j, Y \a\t g:i:s A') }}
                        </td>
                        <td>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('file.index', $folder->id) }}" class="btn btn-sm btn-primary"><i
                                        class="fas fa-folder-open"></i>
                                    Open</a>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-secondary ml-1"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis"></i>
                                    </button>
                                    <ul class="dropdown-menu" id="dropdownmenu{{ $folder->id }}">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#renameFolderModal{{ $folder->id }}"><i
                                                    class="fas fa-edit"></i>
                                                Rename</a>
                                        </li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#shareFolderModal{{ $folder->id }}"><i
                                                    class="fas fa-share-nodes"></i>
                                                Share</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal"
                                                data-bs-target="#removeFolderModal"
                                                data-url="{{route('folder.destroy', $folder->id)}}"
                                                id="btn-delete-folder"><i class="fas fa-trash"></i>
                                                Delete</a>
                                        </li>
                                    </ul>
                                    @include('folders.modal._rename')
                                    @include('folders.modal._share')
                                </div>

                                {{-- Delete Confirm Modal --}}
                                <div class="modal fade" id="removeFolderModal" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="removeFolderLabel">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{route('folder.destroy', $folder->id)}}" method="POST"
                                                id="removeFolderModalForm">
                                                @method('DELETE')
                                                @csrf
                                                <div class="modal-body">
                                                    Are you sure you want to delete this folder?
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
                    @empty

                    @endforelse
                    @forelse ($files as $file)
                    <tr class="table-row-pointer">
                        <td>{{ $file->fileName }}</td>
                        <td>{{ $file->user == NULL ? 'Deleted user' : $file->user->full_name() }}</td>
                        <td>{{ $file->created_at->format('M j, Y \a\t g:i:s A') }}</td>
                        <td>
                            <div class="d-flex justify-content-end">
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
                                                    class="fas fa-trash"></i>
                                                Delete</a>
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
                    @empty

                    @endforelse
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
                {
                    targets: [3],
                    orderable: false,
                },
            ],
            "order": [
                [ 0, 'asc' ],
                [ 2, 'desc' ]
            ],
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
    $(document).on('click', '#btn-delete-folder', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        $('#removeFolderModalForm').attr('action', url);
    });
</script>

<script>
    $(document).ready(function(){
        $('#search').on('keyup', function(){
            event.preventDefault();
            var search = $('#search').val();
            var folder = '{{ $current_folder?->id }}';
            $.ajax({
                type: 'get',
                url: '{{ route('file.search') }}',
                data: {'search':search,'folder':folder},
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
        },
        dropOnElement: false,
        dropOnPage: true,
    });
    FilePond.create(document.querySelector('input[name="file[]"]'), {chunkUploads: true});

    var filecount = 0;

    document.addEventListener('FilePond:processfile', (e) => {
        filecount += 1;
        // console.log('File processed: ' + filecount);
    });

    document.addEventListener('FilePond:removefile', (e) => {
        filecount -= 1;
        // console.log('Files: ' + filecount);
        if(filecount < 1){
            $('#uploadButton').addClass('d-none');
        }
    });

    document.addEventListener('FilePond:processfiles', (e) => {
        $('#uploadButton').removeClass('d-none');
    });
</script>
@endsection