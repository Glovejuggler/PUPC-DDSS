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
                        <form action="{{ route('user.store') }}" method="post" class="needs-validation" novalidate>
                            @csrf
                            <div class="modal-body">
                                <label for="first_name" class="form-label">First name</label>
                                <div class="input-group mb-2">
                                    <input type="text" name="first_name" class="form-control" id="first_name"
                                        aria-describedby="basic-addon3" required value="{{ old('first_name') }}">
                                </div>

                                <label for="last_name" class="form-label">Last name</label>
                                <div class="input-group mb-2">
                                    <input type="text" name="last_name" class="form-control" id="last_name"
                                        aria-describedby="basic-addon3" required value="{{ old('last_name') }}">
                                </div>

                                <label for="middle_name" class="form-label">Middle name</label>
                                <div class="input-group mb-2">
                                    <input type="text" name="middle_name" class="form-control" id="middle_name"
                                        aria-describedby="basic-addon3" value="{{ old('middle_name') }}">
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
                                    <input type="text" name="email" class="form-control {{ Session::get('invalid') }}"
                                        id="email" aria-describedby="basic-addon3" required>
                                </div>
                                <div>
                                    <span id="error_email"></span>
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
                                <button type="submit" class="btn btn-primary" id="register">Submit</button>
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
                                    id="btn-delete-user" {{ $user->id == 1 ? 'disabled' : '' }}><i
                                        class="fas fa-trash"></i></button>

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

@if(!empty(Session::get('error_code')) && Session::get('error_code') == 5)
<script>
    $(function() {
    $('#addUserModal').modal('show');
});
</script>
@endif

<script>
    $(document).on('click', '#btn-delete-user', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        $('#removeUserModalForm').attr('action', url);
    });
</script>

<script>
    (function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
        form.addEventListener('submit', function(event) {
            if (form.checkValidity() === false) {
            event.preventDefault();
            event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
        });
    }, false);
    })();
</script>

<script>
    $(document).ready(function(){

        $('#email').blur(function(){
            var error_email = '';
            var email = $('#email').val();
            var _token = $('input[name="_token"]').val();
            var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(!filter.test(email)) {    
                $('#error_email').html('<label class="text-danger">Invalid email</label>');
                $('#email').addClass('has-error');
                $('#register').attr('disabled', 'disabled');
            } else {
                $.ajax({
                    url:"{{ route('email.check') }}",
                    method:"POST",
                    data:{email:email, _token:_token},
                    success:function(result) {
                        if(result == 'unique') {
                            $('#error_email').html('<label class="text-success">Email available</label>');
                            $('#email').removeClass('has-error');
                            $('#email').removeClass('is-invalid');
                            $('#register').attr('disabled', false);
                        } else {
                            $('#error_email').html('<label class="text-danger">This email is already taken</label>');
                            $('#email').addClass('has-error');
                            $('#email').addClass('is-invalid');
                            $('#register').attr('disabled', 'disabled');
                        }
                    }
                })
            }
        });
    });
</script>
@endsection