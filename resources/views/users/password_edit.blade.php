@extends('layouts.master')

@section('content')
<div class="container">
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