
@if(!$checkin)
<div></div>
@else
    @if($checkin->checkin_time=="0")
    @else  
    <div>{{$checkin->checkin_time}}</div>
    @endif
@endif