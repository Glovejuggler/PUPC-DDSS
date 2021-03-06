@extends('layouts.master')

@section('css')
<style>
    #contentWrapper {
        background-image: url('{{ asset('images/PUP-bg-transparent.png') }}');
        background-size: cover;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mt-2">
        @can('do-admin-stuff')
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $userCount }}</h3>
                    <p>Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('user.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        @endcan
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $filecount }}</h3>
                    <p>Files</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file"></i>
                </div>
                <a href="{{ route('file.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    @if ($filecount != 0)
    @can('do-admin-stuff')
    <div class="row">
        <div class="col-6">
            <div class="card card-danger">
                <div class="card-header">
                    <h3 class="card-title">Files</h3>
                </div>
                <div class="card-body d-flex justify-content-center">
                    <div style="position: relative; width: 40vw">
                        <canvas id="myChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
    @endif
</div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/chartjs.js') }}"></script>

<script>
    const data = {
        labels: [
        @foreach ($roles as $role)
            '{{ $role->roleName }}',
        @endforeach
        ],
        datasets: [{
            label: 'Files',
            data: [
                @foreach ($count as $roleFileCount)
                    {{ $roleFileCount }},
                @endforeach
            ],
            backgroundColor: [
                'rgb(54, 162, 235)',
                'rgb(255, 99, 132)',
                'rgb(251, 86, 7)',
                'rgb(89, 46, 131)',
                'rgb(155, 193, 188)',
                'rgb(67, 170, 139)',
                'rgb(112, 5, 72)',
                'rgb(245, 233, 96)',
            ],
            hoverOffset: 4
        }]
    };

    const config = {
        type: 'pie',
        data: data,
        options: {
            responsive: true, 
            maintainAspectRatio: false,
        }
    };
</script>

<script>
    const myChart = new Chart(
        document.getElementById('myChart'),
        config
    );

</script>

@endsection