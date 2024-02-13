<div class="main-sidebar sidebar-style-2">
    <!-- background-color: #AED6F1 -->
    <aside id="sidebar-wrapper">
        @if(auth()->user()->role->name === "Admin")
        <div class="en sidebar-brand" data="admin_panel" id="subtitle-family">

        </div>
        @else
        <div class="en sidebar-brand" data="accounta_panel" id="subtitle-family"> </div>

        @endif

        <ul class="sidebar-menu">
            @if(auth()->user()->role->name === "Admin")
            <li class="{{ request()->segment(2) == 'dashboard' ? 'active' : '' }}">
                <a href="{{ url('admin/dashboard') }}" class="nav-link"><i class="fas fa-tachometer-alt"></i> <span class="en" data="dashboard" id="subtitle-family"></span></a>
            </li>
            @else
            <div></div>
            @endif

            @if(auth()->user()->role->name === "Admin")
            <li class="{{ request()->segment(2) == 'users' ? 'active' : '' }}">
                <a href="{{ url('admin/users') }}" class="nav-link"><i class="fas fa-users"></i> <span class="en" data="" id="subtitle-family">User</span></a>
            </li>
            @else
          
            @endif
            @if(auth()->user()->role->name === "Admin")
            <li class="{{ request()->segment(2) == 'reports' ? 'active' : '' }}">
                <a href="{{ url('admin/reports') }}" class="nav-link"><i class="fas fa-users"></i> <span class="en" data="" id="subtitle-family">Report</span></a>
            </li>
            @else
          
            @endif
            @if(auth()->user()->role->name === "Admin")
            <li class="{{ request()->segment(2) == 'holidays' ? 'active' : '' }}">
                <a href="{{ url('admin/holidays') }}" class="nav-link"><i class="fas fa-users"></i> <span class="en" data="" id="subtitle-family">Holiday</span></a>
            </li>
            @else
          
            @endif
            <!-- for normall user -->
            @if(auth()->user()->role->name === "Admin")
            <li class="{{ request()->segment(2) == 'qrs' ? 'active' : '' }}">
                <a href="{{ url('admin/qrs') }}" class="nav-link"><i class="fas fa-users"></i> <span class="en" data="" id="subtitle-family">QR</span></a>
            </li>
            @else
          
            @endif
            

            
        </ul>

    </aside>
</div>