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
            <table class="hover compact" id="shareIndexTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Uploader</th>
                        <th>Shared by</th>
                        <th>Date uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($folders as $folder)
                    <tr>
                        <td><a href="{{ route('share.view', $folder->folder->id) }}"
                                class="text-decoration-none text-dark"><i class="fas fa-folder text-yellow"></i>
                                {{ $folder->folder->folderName }}</a>
                        </td>
                        <td>{{ $folder->folder->user->full_name() }}</td>
                        <td>{{ $folder->user->full_name() }}</td>
                        <td>{{ $folder->folder->created_at->format('M j, Y \a\t g:i:s A') }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('share.view', $folder->folder->id) }}"
                                    class="btn btn-sm btn-primary"><i class="fas fa-folder-open"></i> Open</a>
                            </div>
                        </td>
                    </tr>
                    @empty

                    @endforelse
                    @foreach ($files as $file)
                    <tr>
                        <td>{{ $file->file->fileName }}</td>
                        <td>{{ $file->file->user == NULL ? 'Deleted user' : $file->file->user->full_name()
                            }}</td>
                        <td>{{ $file->user == NULL ? 'Deleted user' : $file->user->full_name() }}</td>
                        <td>{{ $file->file->created_at->format('M j, Y \a\t g:i:s A') }}</td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('file.download', $file->file->id) }}" download
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4 d-flex justify-content-end">
                {{ $files->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready( function () {
            $('#shareIndexTable').DataTable({
                order: [[3, 'desc']],
                paging: false,
                info: false,
            });
        });
</script>

<script>
    $(document).on('click', '#btn-delete-file', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        $('#removeFileModalForm').attr('action', url);
    });
</script>
@endsection