@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ url('css/simple-calendar.css') }}" rel="stylesheet">

@endpush


@push('scripts')
<script src="{{ url('js/jquery.simple-calendar.min.js') }}"></script>
<script>
    $(document).ready(function(){
        var ev = JSON.parse('@php echo json_encode($events) @endphp');
        $("#calendar").simpleCalendar({
            displayEvent: true,
            // events: [{
            //     startDate:new Date(new Date().setHours(new Date().getHours() + 24)).toDateString(),
            //     endDate:new Date(new Date().setHours(new Date().getHours() + 25)).toISOString(),
            //     summary:'Visit of the Eiffel Tower'
            // }]
            events: ev
        });
    });
</script>
@endpush