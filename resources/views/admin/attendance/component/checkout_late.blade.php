@if(!$checkin)
<div></div>
@else
    @if($checkin->checkout_status=='too fast' || $checkin->checkout_status=='too early')
    <p style="background: #f44336;border-radius: 15px;text-align: center;color:#FFFFFF;">{{$checkin->checkout_late}}</p> 
    @elseif($checkin->checkout_status=='good')
    <div>{{$checkin->checkout_late}}</div>
    @elseif($checkin->checkin_status=='absent')
    <div></div>
    @else
    <p style="background: #5ad13c;border-radius: 15px;text-align: center;color:#FFFFFF;">{{$checkin->checkout_late}}</p>
    @endif
@endif

