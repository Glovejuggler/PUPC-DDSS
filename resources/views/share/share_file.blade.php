@extends('layouts.master')

@section('content')

<div class="container">
    <h3>{{ $file->fileName }}</h3>
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Share this file</h3>
        </div>
        <form action="{{ route('share.sharefile') }}" method="post">
            @csrf
            @method('POST')
            <div class="card-body">
                <input type="text" class="form-control" name="file_id" value="{{ $file->id }}" hidden>
                @forelse ($roles as $role)
                @if ($role->id != Auth::user()->role_id)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="{{ $role->id }}" name="role_id[]"
                        id="checkbox{{ $role->id }}" @foreach ($shares as $share) {{ $share->role_id ==
                    $role->id ? 'checked' : '' }}
                    @endforeach>
                    <label class="form-check-label" for="checkbox{{ $role->id }}">
                        {{ $role->roleName }}
                    </label>
                </div>
                @endif
                @empty
                <p>No other roles to share this file to.</p>
                @endforelse
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-primary" type="submit">Share</button>
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection