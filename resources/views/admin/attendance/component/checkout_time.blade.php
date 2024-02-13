
@if(!$checkin)
<div></div>
@else
    @if($checkin->checkout_time=="0")
    @else  
    <div>{{$checkin->checkout_time}}</div>
    @endif
    
@endif

