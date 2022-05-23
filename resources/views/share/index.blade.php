@inject('request', 'Illuminate\Http\Request')
@extends('layouts.master')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Shared Files</h3>
        </div>
        <div class="card-body">
            <hr>

            <table class="table table-bordered datatable dt-select" id="myTable">
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
                        <td>{{ $file->user == NULL ? 'Deleted user' : $file->user->first_name.'
                            '.$file->user->last_name
                            }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('file.download', $file->id) }}" download
                                    class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
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