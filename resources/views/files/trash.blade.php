@inject('request', 'Illuminate\Http\Request')
@extends('layouts.master')

@section('content')
<div class="container">
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Trash</h3>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end">
                <a href="{{ request('grid') == 1 ? Request::url() : Request::url().'?grid=1' }}"
                    class="text-dark text-decoration-none"><i
                        class="fas {{ request('grid') == 1 ? 'fa-list' : 'fa-th' }}"></i> {{ request('grid') == 1 ?
                    'List view' : 'Grid view' }}</a>
            </div>
            <hr>
            @if (request('grid') == 1)
            <div class="row d-flex justify-content-end px-3">
                <div class="input-group my-2" style="width: 25%;">
                    <input type="text" class="form-control" placeholder="Search" aria-label="Search"
                        aria-describedby="button-addon2" name="search" id="search">
                </div>
            </div>
            <div class="row mt-2 px-3 justify-content-start" id="gridView">
                @include('files.partials.trash_gridview')
            </div>
            @else
            @include('files.partials.trash_listview')
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready( function () {
        $('#fileIndexTable').DataTable({
            columnDefs: [
                {
                    targets: [4],
                    orderable: false,
                },
            ],
            order: [[4, 'asc'], [2, 'desc']],
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

<script>
    $(document).ready(function(){
        $('#search').on('keyup', function(){
            event.preventDefault();
            var search = $('#search').val();
            $.ajax({
                type: 'get',
                url: '{{ route('trash.search') }}',
                data: {'search':search},
                success: function(data) {
                    // console.log(data);
                    $('#gridView').html(data);
                },
            })
        });
    });
</script>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
@endsection