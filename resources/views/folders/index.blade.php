@inject('request', 'Illuminate\Http\Request')
@extends('layouts.master')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Folders</h3>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal"
                    data-bs-target="#addFolderModal"><i class="fas fa-folder-plus"></i> Add new folder</button>
                @can('do-admin-stuff')
                <nav class="nav ml-auto">
                    <a class="nav-link" href="{{ route('folder.index') }}"
                        style="{{ request('show_deleted') == 1 ? '' : 'font-weight: 700' }}">All</a>
                    <a class="nav-link" href="{{ route('folder.index') }}?show_deleted=1"
                        style="{{ request('show_deleted') == 1 ? 'font-weight: 700' : '' }}">Trash</a>
                </nav>
                @endcan
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

            <table class="table table-bordered dataTable" id="myTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($folders as $folder)
                    <tr>
                        <td>{{ $folder->folderName }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                @can('do-admin-stuff')
                                @if (request('show_deleted') == 1)
                                <a href="{{ route('folder.recover', $folder->id) }}"
                                    class="btn btn-sm btn-success mr-1">Restore</a>
                                @endif
                                @endcan
                                <a href="{{ route('folder.show', $folder->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="submit" class="btn btn-danger btn-sm mx-1" data-bs-toggle="modal"
                                    data-bs-target="#removeFolderModal"
                                    data-url="{{route('folder.destroy', $folder->id)}}" id="btn-delete-folder">
                                    <i class="fas fa-trash"></i>
                                </button>

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
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).on('click', '#btn-delete-folder', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        $('#removeFolderModalForm').attr('action', url);
    });
</script>
@endsection