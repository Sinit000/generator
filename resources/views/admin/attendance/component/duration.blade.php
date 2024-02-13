
@if(!$checkin)
<div></div>
@else
    @if($checkin->duration =="0")
    @else
        <div>{{$checkin->duration}}</div>
    @endif

@endif