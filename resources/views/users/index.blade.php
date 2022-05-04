@extends('layouts.master')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Users</h3>
        </div>
        <div class="card-body">
            <button type="button" class="btn btn-sm btn-primary mb-2" data-bs-toggle="modal"
                data-bs-target="#addUserModal"><i class="fas fa-user-plus"></i> Add new user</button>

            {{-- Add User Modal --}}
            <div class="modal fade" id="addUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
                aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addUserModalLabel">Add new user</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('user.store') }}" method="post">
                            @csrf
                            <div class="modal-body">
                                <label for="first_name" class="form-label">First name</label>
                                <div class="input-group mb-2">
                                    <input type="text" name="first_name" class="form-control" id="first_name"
                                        aria-describedby="basic-addon3" required>
                                </div>

                                <label for="last_name" class="form-label">Last name</label>
                                <div class="input-group mb-2">
                                    <input type="text" name="last_name" class="form-control" id="last_name"
                                        aria-describedby="basic-addon3" required>
                                </div>

                                <label for="middle_name" class="form-label">Middle name</label>
                                <div class="input-group mb-2">
                                    <input type="text" name="middle_name" class="form-control" id="middle_name"
                                        aria-describedby="basic-addon3">
                                </div>

                                <label for="address" class="form-label">Address</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="basic-addon3"><i
                                            class="fas fa-location-arrow"></i></span>
                                    <input type="text" name="address" class="form-control" id="address"
                                        aria-describedby="basic-addon3" required>
                                </div>

                                <label for="role" class="form-label">Role</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                    <select name="role" class="form-select" aria-label="Default select example"
                                        id="role" required>
                                        <option selected disabled hidden value="">Select a role...
                                        </option>
                                        @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->roleName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <label for="email" class="form-label">Email</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="basic-addon3"><i class="fas fa-at"></i></span>
                                    <input type="text" name="email" class="form-control" id="email"
                                        aria-describedby="basic-addon3" required>
                                </div>

                                <label for="password" class="form-label">Password</label>
                                <div class="input-group mb-2">
                                    <span class="input-group-text" id="basic-addon3"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" id="password"
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
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Address</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->first_name.' '.$user->middle_name.' '.$user->last_name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->address }}</td>
                        <td>{{ $user->role==NULL ? 'Unassigned' : $user->role->roleName }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('user.show', $user->id) }}">
                                    <button type="button" class="btn btn-sm btn-primary ml-1"><i
                                            class="fas fa-eye"></i></button></a>
                                <button type="button" class="btn btn-sm btn-danger ml-1" data-bs-toggle="modal"
                                    data-bs-target="#removeUserModal" data-url="{{route('user.destroy', $user->id)}}"
                                    id="btn-delete-user"><i class="fas fa-trash"></i></button>

                                {{-- Delete Confirm Modal --}}
                                <div class="modal fade" id="removeUserModal" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="removeUserLabel">Confirmation</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{route('user.destroy', $user->id)}}" method="POST"
                                                id="removeUserModalForm">
                                                @method('DELETE')
                                                @csrf
                                                <div class="modal-body">
                                                    Are you sure you want to delete this user?
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
    $(document).on('click', '#btn-delete-user', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        $('#removeUserModalForm').attr('action', url);
    });
</script>
@endsection