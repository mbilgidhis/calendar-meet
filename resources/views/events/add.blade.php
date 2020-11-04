@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Create Event</div>

                <div class="card-body">
                    @include('layouts.flash')
                    @if( Auth::user()->token )
                    <form action="{{ route('events.save') }}" method="post">
                        @csrf
                        <div class="form-group row">
                            <label for="name" class="col-sm-2 col-form-label">Event Name</label>
                            <div class="col-sm-10">
                                <input type="text" name="name" id="name" class="form-control" placeholder="Event Name" required autocomplete="off" autofocus>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="start" class="col-sm-2 col-form-label">Start</label>
                            <div class="col-sm-10">
                                <div class="input-group date" id="startpicker" data-target-input="nearest">
                                    <input type="text" name="start" id="start" class="form-control datetimepicker-input" data-target="#startpicker" placeholder="Start Time" required autocomplete="off">
                                    <div class="input-group-append" data-target="#startpicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="end" class="col-sm-2 col-form-label">End</label>
                            <div class="col-sm-10">
                                <div class="input-group date" id="endpicker" data-target-input="nearest">
                                    <input type="text" name="end" id="end" class="form-control datetimepicker-input" data-target="#endpicker" placeholder="End Time" required autocomplete="off">
                                    <div class="input-group-append" data-target="#endpicker" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="description" class="col-sm-2 col-form-label">Description</label>
                            <div class="col-sm-10">
                                <textarea name="description" id="description" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="offset-sm-9 col-sm-3 text-right">
                                {!! RecaptchaV3::field('add') !!}
                                <input type="submit" class="btn btn-success btn-sm" value="Save">
                            </div>
                        </div>
                    </form>
                    @else
                        <p>Please create token first by accessing this <a href="{{ route('get-token') }}">link</a>.</p>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/js/tempusdominus-bootstrap-4.min.js" integrity="sha512-2JBCbWoMJPH+Uj7Wq5OLub8E5edWHlTM4ar/YJkZh3plwB2INhhOC3eDoqHm1Za/ZOSksrLlURLoyXVdfQXqwg==" crossorigin="anonymous"></script>
<script>
    $(document).ready(function() {
        $.fn.datetimepicker.Constructor.Default = $.extend({}, $.fn.datetimepicker.Constructor.Default, {
            icons: {
                time: 'far fa-clock',
                date: 'far fa-calendar',
                up: 'far fa-arrow-up',
                down: 'far fa-arrow-down',
                previous: 'far fa-chevron-left',
                next: 'far fa-chevron-right',
                today: 'far fa-calendar-check-o',
                clear: 'far fa-trash',
                close: 'far fa-times'
            } });
        $('#startpicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
        });

        $('#endpicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
        });
    });
</script>
@endpush

@push('styles')
{!! RecaptchaV3::initJs() !!}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.1.2/css/tempusdominus-bootstrap-4.min.css" integrity="sha512-PMjWzHVtwxdq7m7GIxBot5vdxUY+5aKP9wpKtvnNBZrVv1srI8tU6xvFMzG8crLNcMj/8Xl/WWmo/oAP/40p1g==" crossorigin="anonymous" />
@endpush