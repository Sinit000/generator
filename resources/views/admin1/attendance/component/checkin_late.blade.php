@if(!$checkin)
<div></div>
@else
    @if($checkin->checkin_status=='late')
    <p style="background: #f44336;border-radius: 15px;text-align: center;color:#FFFFFF;">{{$checkin->checkin_late}}</p> 
    @elseif($checkin->checkin_status=='on time')
    <p style="border-radius: 15px;text-align: center;">{{$checkin->checkin_late}}</p>

    @else
    <p style="background: #5ad13c;border-radius: 15px;text-align: center;color:#FFFFFF;">{{$checkin->checkin_late}}</p>
    @endif
@endif