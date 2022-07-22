@extends('layouts.master')

@section('css')
<style>
    .avatar-container {
        position: relative;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        overflow: hidden;
        background-color: #111;
        cursor: pointer;
    }

    .avatar-container:hover .avatar-content {
        opacity: 1;
    }

    .avatar-container:hover .avatar-img {
        opacity: 0.5;
    }

    .avatar-img {
        object-fit: cover;
        opacity: 1;
        transition: opacity .2s ease-in-out;
    }

    .avatar-content {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: white;
        opacity: 0;
        transition: opacity .2s ease-in-out;
    }

    .avatar-icon {
        color: white;
        padding-bottom: 8px;
    }

    .avatar-icon.fas {
        font-size: 20px;
    }

    .avatar-text {
        text-transform: uppercase;
        font-size: 16px;
        width: 60%;
        text-align: center;
        color: white;
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between">
            <h3 class="card-title">Your Profile</h3>
        </div>
        <div class="card-body">
            <div class="row d-flex flex-row justify-content-center">
                <div class="col-auto">
                    <div class="d-flex justify-content-center avatar-container">
                        <img src="{{ DDSS::getAvatar(Auth::user()->id, 250) }}" alt="" class="avatar-img">
                        <div class="avatar-content" data-bs-toggle="modal" data-bs-target="#avatarChange">
                            <span class="avatar-icon"><i class="fas fa-camera"></i></span>
                            <span class="avatar-text">Change profile picture</span>
                        </div>
                    </div>
                    <a href="{{ route('password.edit') }}"
                        class="btn btn-sm btn-outline-danger d-sm-none d-none d-md-block mt-4 mb-2">Change
                        password</a>
                </div>
                <div class="ml-lg-4 col-lg-6">
                    <a href="{{ route('password.edit') }}"
                        class="btn btn-sm btn-outline-danger d-sm-block d-md-none d-block mt-4 mb-2">Change
                        password</a>
                    <form action="{{ route('profile.update') }}" method="post">
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
                                aria-describedby="basic-addon3" value="{{ $user->address }}" required>
                        </div>

                        <label for="role" class="form-label">Role</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                            <select name="role" class="form-select" aria-label="Default select example" id="role"
                                disabled>
                                <option selected hidden>{{ $user->role == NULL ? 'Unassigned' : $user->role->roleName }}
                                </option>
                            </select>
                        </div>

                        <label for="email" class="form-label">Email</label>
                        <div class="input-group mb-2">
                            <span class="input-group-text" id="basic-addon3"><i class="fas fa-at"></i></span>
                            <input type="text" name="email" class="form-control" id="email"
                                aria-describedby="basic-addon3" value="{{ $user->email }}" required>
                        </div>
                        <button type="submit" class="col-md-auto btn btn-sm btn-success mt-2">Update</button>
                    </form>
                </div>
            </div>

            {{-- Change Profile Pic Modal --}}
            <div class="modal fade" id="avatarChange" tabindex="-1" aria-labelledby="avatarChangeLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="avatarChangeLabel">Change profile picture</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('avatar.change') }}" enctype="multipart/form-data" method="post"
                                id="upload-image">
                                @csrf
                                <div class="mb-3">
                                    <label for="formFile" class="form-label">Choose an image file</label>
                                    <input class="form-control" type="file" accept="image/*" id="image-input"
                                        name="image" onchange="showpfp(this)" required>
                                </div>
                                <div class="row d-flex justify-content-center">
                                    <div class="d-flex justify-content-center rounded-circle overflow-hidden"
                                        style="max-width: 250px;">
                                        <img id="preview-image-before-upload"
                                            src="{{ DDSS::getAvatar(Auth::user()->id, 250) }}" alt="preview image"
                                            style="max-height: 250px;">
                                    </div>
                                </div>
                                <div class="alert bg-dark mt-4 alert-dismissible fade show" role="alert">
                                    <span><strong class="text-primary">Tip:</strong> Use an image with 1:1 ratio for
                                        better
                                        outcome</span>
                                    <br>
                                    <span><strong class="text-warning">Warning:</strong> The preview may be different
                                        with the actual
                                        result</span>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary"
                                data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-sm btn-primary">Upload</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Your Files</h3>
        </div>
        <div class="card-body" style="overflow-x: auto">
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
                                <a href="{{ route('file.download', $file->id) }}" class="btn btn-sm btn-primary">
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
    function showpfp(el){
        event.preventDefault();
        var reader = new FileReader();
        reader.onload = function(e){
            console.log(e.target.result);
            $('#preview-image-before-upload').attr('src', e.target.result); 
        }
        reader.readAsDataURL(el.files[0]);
    }
</script>

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