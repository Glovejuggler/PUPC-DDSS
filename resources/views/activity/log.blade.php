@extends('layouts.master')

@section('content')
<div class="container">
    <table class="table hover compact">
        <thead>
            <th>Activity</th>
            <th></th>
        </thead>
        <tbody>
            @forelse ($activities as $activity)
            <tr>
                <td><img src="{{ DDSS::getAvatar($activity->causer->id, 50) }}" alt="" class="rounded-circle mr-2"
                        style="width: 25px">{{ $activity->causer->full_name() }} {!! DDSS::activity($activity) !!}
                </td>
                <td>{{ $activity->created_at->format('M j, Y \a\t g:i:s A') }}</td>
            </tr>
            @empty

            @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-end">
        {{ $activities->links() }}
    </div>
</div>
@endsection