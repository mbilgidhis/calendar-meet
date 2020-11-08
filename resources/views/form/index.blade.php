@extends('layouts.app-form')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Join Meeting</div>

                <div class="card-body">
                    <div class="jumbotron">
                        <h5 class="display-5">{{ $event->name }}</h5>
                        <p>{{ $event->description }}</p>
                        @if( $event->active == 1 )
                            @if( count($event->attendees) <= 100 )
                            <p>Event Link: <a href="{{ $event->event_link }}" target="_blank">Click Me!</a></p>
                            <p>Google Meet Link: <a href="{{ $event->meet_link }}" target="_blank">Click Me!</a></p>
                            @else
                            <p>We're sorry. we're not accepting participant right now.</p>
                            @endif
                        @else
                            <p>We're sorry. we're not accepting participant right now.</p>
                        @endif
                    </div>
                    @include('layouts.flash')
                    @if( $event->active )
                        @if( count($event->attendees) <= 100 )
                        <form action="{{ route('form.add', [ 'eventid' => $event->event_id ]) }}" method="post">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->event_id }}">
                            <div class="form-group row">
                                <label for="name" class="col-sm-2 col-form-label">Email</label>
                                <div class="col-sm-10">
                                    <input type="text" name="email" id="email" class="form-control" placeholder="Your Email" required autocomplete="off" autofocus>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="offset-sm-9 col-sm-3 text-right">
                                    {!! RecaptchaV3::field('join') !!}
                                    <button type="submit" class="btn btn-success btn-sm">Save</button>
                                </div>
                            </div>
                        </form>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{!! RecaptchaV3::initJs() !!}
@endpush
