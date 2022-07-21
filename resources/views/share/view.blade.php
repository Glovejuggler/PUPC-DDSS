@inject('request', 'Illuminate\Http\Request')
@extends('layouts.master')

@section('css')
<style>
    .table-row-pointer:hover {
        cursor: default;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">
                {{ $folder->folderName }}
            </h3>
        </div>
        <div class="card-body overflow-hidden">
            <div class="d-flex justify-content-end">
                <a href="{{ request('grid') == 1 ? Request::url() : Request::url().'?grid=1' }}"
                    class="text-dark text-decoration-none"><i
                        class="fas {{ request('grid') == 1 ? 'fa-list' : 'fa-th' }}"></i> {{
                    request('grid') == 1 ?
                    'List view' : 'Grid view' }}</a>
            </div>
            <hr>
            <a href="{{ route('share.index') }}" class="btn btn-sm btn-outline-secondary mb-4"><i
                    class="fas fa-level-up"></i> Back</a>
            @if (request('grid') == 1)
            <div class="row d-flex justify-content-end px-3">
                <div class="input-group my-2" style="width: 25%;">
                    <input type="text" class="form-control" placeholder="Search" aria-label="Search"
                        aria-describedby="button-addon2" name="search" id="search">
                </div>
            </div>
            <div class="row mt-2 pl-2" id="gridView">
                @include('share.partials.gridview')
            </div>
            @else
            <table class="hover compact" id="fileIndexTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Uploader</th>
                        <th>Date uploaded</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($files as $file)
                    <tr class="table-row-pointer">
                        <td>{{ $file->fileName }}</td>
                        <td>{{ $file->user == NULL ? 'Deleted user' : $file->user->full_name() }}</td>
                        <td>{{ $file->created_at->format('M j, Y \a\t g:i:s A') }}</td>
                        <td>
                            <div class="d-flex justify-content-end">
                                <a href="{{ route('file.download', $file->id) }}" download
                                    class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty

                    @endforelse
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready( function () {
        var table = $('#fileIndexTable').DataTable({
            columnDefs: [
                {
                    targets: [3],
                    orderable: false,
                },
            ],
            "order": [
                [ 0, 'asc' ],
                [ 2, 'desc' ]
            ],
            info: false,
        });
    });
</script>
@endsection