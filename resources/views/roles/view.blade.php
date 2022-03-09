@extends('layouts.master')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">{{ $role->roleName }}</h3>
        </div>
        <div class="card-body">

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