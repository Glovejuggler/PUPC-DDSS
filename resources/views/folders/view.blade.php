@extends('layouts.master')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Files in {{ $folder->folderName }} folder</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered dataTable" id="myTable">
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
                        <td>{{ $file->user == NULL ? 'Deleted user' : $file->user->first_name.' '.$file->user->last_name
                            }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="#" class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button type="submit" class="btn btn-danger btn-sm mx-1" data-bs-toggle="modal"
                                    data-bs-target="#removeFileModal" data-url="{{route('file.destroy', $file->id)}}"
                                    id="btn-delete-file">
                                    <i class="fas fa-trash"></i>
                                </button>

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
</script>
@endsection