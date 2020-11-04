@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Event List</div>

                <div class="card-body">
                    @include('layouts.flash')
                    <div class="table-responsive">
                        <table class="table table-hover table-stripped">
                            <thead>
                                <tr class="text-center">
                                    <th>Event Name</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Event Link</th>
                                    <th>Meet Link</th>
                                    <th>Form Link</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach( $events as $event)
                                <tr>
                                    <td>{{ $event->name }}</td>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->start_at)->format('D, d M Y H:i') }}</td>
                                    <td>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $event->end_at)->format('D, d M Y H:i') }}</td>
                                    <td><a href="{{ $event->event_link }}" target="_blank" class="btn btn-primary btn-sm">Open</a></td>
                                    <td><a href="{{ $event->meet_link }}" target="_blank" class="btn btn-primary btn-sm">Open</a></td>
                                    <td><a href="{{ route('form', [ 'id' => $event->id, 'eventid' => $event->event_id]) }}" target="_blank" class="btn btn-primary btn-sm">Link</a></td>
                                    <td><a href="{{ route('events.edit', [ 'id' => $event->id]) }}" class="btn btn-warning btn-sm">Edit</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    {{ $events->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection