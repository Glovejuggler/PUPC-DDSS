@extends('layouts.master')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between">
            <h3 class="card-title">User Information</h3>
            <a class="card-title ml-auto" href="{{ route('user.edit', $user->id) }}">
                <h3 class="card-title"><i class="fas fa-edit"></i> Edit</h3>
            </a>
        </div>
        <div class="card-body">
            <div class="row d-flex justify-content-center">
                <div class="col-6 md-auto">
                    <label for="first_name" class="form-label">First name</label>
                    <div class="input-group mb-2">
                        <input type="text" name="first_name" class="form-control" id="first_name"
                            aria-describedby="basic-addon3" value="{{ $user->first_name }}" disabled>
                    </div>

                    <label for="last_name" class="form-label">Last name</label>
                    <div class="input-group mb-2">
                        <input type="text" name="last_name" class="form-control" id="last_name"
                            aria-describedby="basic-addon3" value="{{ $user->last_name }}" disabled>
                    </div>

                    <label for="middle_name" class="form-label">Middle name</label>
                    <div class="input-group mb-2">
                        <input type="text" name="middle_name" class="form-control" id="middle_name"
                            aria-describedby="basic-addon3" value="{{ $user->middle_name }}" disabled>
                    </div>

                    <label for="address" class="form-label">Address</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text" id="basic-addon3"><i class="fas fa-location-arrow"></i></span>
                        <input type="text" name="address" class="form-control" id="address"
                            aria-describedby="basic-addon3" value="{{ $user->address }}" disabled>
                    </div>

                    <label for="role" class="form-label">Role</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                        <select name="role" class="form-select" aria-label="Default select example" id="role" disabled>
                            <option selected hidden>{{ $user->role == NULL ? 'Unassigned' : $user->role->roleName }}
                            </option>
                        </select>
                    </div>

                    <label for="email" class="form-label">Email</label>
                    <div class="input-group mb-2">
                        <span class="input-group-text" id="basic-addon3"><i class="fas fa-at"></i></span>
                        <input type="text" name="email" class="form-control" id="email" aria-describedby="basic-addon3"
                            value="{{ $user->email }}" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">{{ $user->first_name.' '.$user->last_name }}'s Files</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered dataTable" id="myTable1">
                <thead>
                    <tr>
                        <th>File</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($files as $file)
                    <tr>
                        <td>{{ $file->fileName }}</td>
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

<script>
    $(document).ready( function () {
            $('#myTable1').DataTable();
        } );
</script>
@endsection