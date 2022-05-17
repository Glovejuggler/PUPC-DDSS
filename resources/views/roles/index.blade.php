@extends('layouts.master')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Roles</h3>
        </div>
        <div class="card-body">
            <button type="button" class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal"
                data-bs-target="#addRoleModal"><i class="fas fa-briefcase"></i> Add new role</button>

            {{-- Add Role Modal --}}
            <div class="modal fade" id="addRoleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="addRoleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addRoleModalLabel">Add new role</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('role.store') }}" method="post">
                            @csrf
                            <div class="modal-body">
                                <label for="roleName" class="form-label">Role name</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="basic-addon3"><i
                                            class="fas fa-briefcase"></i></span>
                                    <input type="text" name="roleName" class="form-control" id="roleName"
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
                    @foreach ($roles as $role)
                    <tr>
                        <td>{{ $role->roleName }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('role.show', $role->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="submit" class="btn btn-danger btn-sm mx-1" data-bs-toggle="modal"
                                    data-bs-target="#removeRoleModal" data-url="{{route('role.destroy', $role->id)}}"
                                    id="btn-delete-role" {{ $role->id == 1 ? 'disabled' : '' }}>
                                    <i class="fas fa-trash"></i>
                                </button>

                                {{-- Delete Confirm Modal --}}
                                <div class="modal fade" id="removeRoleModal" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="removeRoleLabel">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{route('role.destroy', $role->id)}}" method="POST"
                                                id="removeRoleModalForm">
                                                @method('DELETE')
                                                @csrf
                                                <div class="modal-body">
                                                    Are you sure you want to delete this role?
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
    $(document).on('click', '#btn-delete-role', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        $('#removeRoleModalForm').attr('action', url);
    });
</script>
@endsection