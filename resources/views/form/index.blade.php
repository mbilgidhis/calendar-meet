@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">Join Meeting</div>

                <div class="card-body">
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
                                <button type="submit" class="btn btn-success btn-sm">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
