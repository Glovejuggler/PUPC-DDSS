@extends('layouts/master')

@section('content')
<div class="container">
    <h3>{{ $file->fileName }}</h3>
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title">Rename this file</h3>
        </div>
        <form action="{{ route('file.rename', $file->id) }}" method="post">
            @csrf
            @method('PUT')
            <div class="card-body">
                <label for="fileName" class="form-label">File name</label>
                <div class="input-group mb-2">
                    <input type="text" name="fileName" class="form-control" id="fileName"
                        aria-describedby="basic-addon3" required value="{{ old('fileName') }}">
                </div>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-primary" type="submit">Rename</button>
                <a href="{{ url()->previous() }}" class="btn btn-sm btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection