@extends('layouts.master')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between">
            <h3 class="card-title">Edit Information</h3>
        </div>
        <div class="card-body">
            <div class="row d-flex justify-content-center">
                <div class="col-6 md-auto">
                    <form action="{{ route('profile.update', $user->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        <label for="first_name" class="form-label">First name</label>
                        <div class="input-group mb-2">
                            <input type="text" name="first_name" class="form-control" id="first_name"
                                aria-describedby="basic-addon3" value="{{ $user->first_name }}" required>
                        </div>

                        <label for="last_name" class="form-label">Last name</label>
                        <div class="input-group mb-2">
                            <input type="text" name="last_name" class="form-control" id="last_name"
                                aria-describedby="basic-addon3" value="{{ $user->last_name }}" required>
                        </div>

                        <label for="middle_name" class="form-label">Middle name</label>
                        <div class="input-group mb-2">
                            <input type="text" name="middle_name" class="form-control" id="middle_name"
                                aria-describedby="basic-addon3" value="{{ $user->middle_name }}">
                        </div>

                        <label for="address" class="form-label">Address</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text" id="basic-addon3"><i
                                    class="fas fa-location-arrow"></i></span>
                            <input type="text" name="address" class="form-control" id="address"
                                aria-describedby="basic-addon3" value="{{ $user->address }}" requried>
                        </div>

                        <label for="role" class="form-label">Role</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                            <select name="role" class="form-select" aria-label="Default select example" id="role"
                                disabled>
                                <option selected hidden value="{{ $user->role_id }}">{{ $user->role == NULL ?
                                    'Unassigned' :
                                    $user->role->roleName }}
                                </option>
                                @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->roleName }}</option>
                                @endforeach
                            </select>
                        </div>

                        <label for="email" class="form-label">Email</label>
                        <div class="input-group mb-4">
                            <span class="input-group-text" id="basic-addon3"><i class="fas fa-at"></i></span>
                            <input type="text" name="email" class="form-control" id="email"
                                aria-describedby="basic-addon3" value="{{ $user->email }}" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-sm btn-success mr-2">Update</button>
                            <a href="{{ url()->previous() }}"><button type="button"
                                    class="btn btn-sm btn-secondary">Cancel</button></a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4" id="changepassword">
        <div class="card-header">
            <h3 class="card-title">Change password</h3>
        </div>
        <form action="{{ route('change_password') }}" method="post" class="needs-validation" novalidate>
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row d-flec justify-content-center">
                    <div class="col-6 md-auto">
                        <label for="old_password" class="form-label">Old password</label>
                        <span class="text-danger">@error('old_password'){{ $message }}@enderror</span>
                        <div class="input-group mb-2">
                            <input type="password" name="old_password" class="form-control" id="old_password"
                                aria-describedby="basic-addon3" value="" required>
                        </div>

                        <label for="new_password" class="form-label">New password</label>
                        <div class="input-group mb-2">
                            <input type="password" name="new_password" class="form-control" id="new_password"
                                aria-describedby="basic-addon3" value="" required>
                        </div>

                        <label for="confirm_password" class="form-label">Confirm new password</label>
                        <span class="text-danger">@error('confirm_password'){{ $message }}@enderror</span>
                        <div class="input-group mb-2">
                            <input type="password" name="confirm_password" class="form-control" id="confirm_password"
                                aria-describedby="basic-addon3" value="" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-sm btn-success mr-2">Change password</button>
                            <a href="{{ url()->previous() }}"><button type="button"
                                    class="btn btn-sm btn-secondary">Cancel</button></a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')

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

@endsection