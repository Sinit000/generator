

@if($profile_url)
    <img src="https://banban-hr.com/hotel/public/{{ $profile_url }}"  width="50" height="50" class="img-rounded"  />
@else
<img src="{{asset('img/users/admin.jpg')}}"  width="50" height="50" class="img-rounded"  />
@endif
