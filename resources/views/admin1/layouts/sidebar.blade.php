<div class="main-sidebar sidebar-style-2" >
<!-- background-color: #AED6F1 -->
    <aside id="sidebar-wrapper" >
        @if(auth()->user()->role->name === "Admin")
        <div class="sidebar-brand">
            Admin Panel
        </div>
        @else
        <div class="sidebar-brand">
            Accountant Panel
        </div>
        @endif
        <div class="sidebar-brand sidebar-brand-sm">
            
        </div>
        <ul class="sidebar-menu" >
                @if(auth()->user()->role->name === "Admin")
                <li class="{{ request()->segment(2) == 'dashboard' ? 'active' : '' }}">
                    <a href="{{ url('admin/dashboard') }}" class="nav-link" ><i class="fas fa-tachometer-alt"></i> <span class="en" data="dashboard" id="subtitle-family"></span></a>
                </li>
                @else
                @endif
                
                @if(auth()->user()->role->name === "Admin")
                    <li class="{{ request()->segment(2) == 'user' ? 'active' : '' }}">
                        <a href="{{ url('admin/user') }}" class="nav-link"><i class="fas fa-users"></i> <span class="en" data="employee" id="subtitle-family"></span></a>
                    </li>
                @else
                
                @endif

               
                @if(auth()->user()->role->name === "Admin")
                <li class="{{ request()->segment(2) == 'attendances' ? 'active' : '' }}">
                    <a href="{{ url('admin/attendances') }}" class="nav-link"><i class="fas fa-tachometer-alt"></i> <span class="en" data="attendances" id="subtitle-family"></span></a>
                </li>
                
                @endif
               
                <!-- approval board -->
                @if(auth()->user()->role->name === "Admin")
                <li  class="nav-item dropdown ">

                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-chart-bar"></i> <span class="en" data="approve_board" id="subtitle-family"></span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ request()->segment(2) == 'overtimes' ? 'active' : '' }}" ><a class="nav-link " href="{{ url('admin/approve/leaves') }}"><span class="en" data="leave" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'leaveouts' ? 'active' : '' }}" ><a class="nav-link " href="{{ url('admin/approve/leaveouts') }}"><span class="en" data="leave_out" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'compesation' ? 'active' : '' }}" ><a class="nav-link " href="{{ url('admin/approve/overtimecompesations') }}"><span class="en" data="clear_ot" id="subtitle-family"></span></a></a></li>
                        <li class="{{ request()->segment(2) == 'dayoff' ? 'active' : '' }}" ><a class="nav-link " href="{{ url('admin/approve/changedayoffs') }}"><span class="en" data="change_day_off" id="subtitle-family"></span></a></li>

                    </ul>
                </li>
                @endif
               
                @if(auth()->user()->role->name === "Admin")
                <li class="{{ request()->segment(2) == 'overtimes' ? 'active' : '' }}">
                    <a href="{{ url('admin/overtimes') }}" class="nav-link"><i class="fas fa-clock fa-xs"></i> <span class="en" data="overtime" id="subtitle-family"></span></a>
                </li> 
                @endif
                
                @if(auth()->user()->role->name === "Admin")
                <li  class="nav-item dropdown ">

                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa fa-dollar"></i><span class="en" data="payroll" id="subtitle-family"></span></a>
                    <ul class="dropdown-menu">

                        
                        <li class="{{ request()->segment(2) == 'structure' ? 'active' : '' }}" ><a class="nav-link " href="{{ url('admin/structure') }}"><span class="en" data="structure" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'contract' ? 'active' : '' }}" ><a class="nav-link " href="{{ url('admin/contract') }}"><span class="en" data="contract" id="subtitle-family"></span></a></li>
                        

                    </ul>
                </li>
                @else
                <li class="{{ request()->segment(2) == 'employees' ? 'active' : '' }}">
                        <a href="{{ url('accountant/report/employees') }}" class="nav-link"><i class="fas fa-users"></i> <span class="en" data="employee" id="subtitle-family"></span></a>
                </li>
                <li class="{{ request()->segment(2) == 'payslip' ? 'active' : '' }}" ><a class="nav-link " href="{{ url('accountant/payslip') }}"><i class="fa fa-dollar"></i><span class="en" data="payroll" id="subtitle-family"></span></a></li>
                <li class="{{ request()->segment(2) == 'ot_acc' ? 'active' : '' }}" ><a class="nav-link " href="{{ url('accountant/report/overtime') }}"><i class="fas fa-clock fa-xs"></i><span class="en" data="overtime_report" id="subtitle-family"></span></a></li>
                <li class="{{ request()->segment(2) == 'att_acc' ? 'active' : '' }}" ><a class="nav-link " href="{{ url('accountant/report/attendances') }}"><i class="fas fa-check-circle"></i><span class="en" data="attendance_report" id="subtitle-family"></span></a></li>
                
                @endif
                @if(auth()->user()->role->name === "Admin")
                <li class="{{ request()->segment(2) == 'reports' ? 'active' : '' }}">
                    <a href="{{ url('admin/reports') }}" class="nav-link"><i class="fas fa-book-open"></i> <span><span class="en" data="report" id="subtitle-family"></span></span></a>
                </li>
                @endif
               
                @if(auth()->user()->role->name === "Admin")

                <li  class="nav-item dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fa fa-cog"></i> <span><span class="en" data="setting" id="subtitle-family"></span></span></a>
                    <ul class="dropdown-menu">
                    
                       
                        <li class="{{ request()->segment(2) == 'location' ? 'active' : '' }}" ><a class="nav-link " href="{{ url('admin/location') }}"><span class="en" data="location" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'workday' ? 'active' : '' }}"><a class="nav-link" href="{{ url('admin/workday') }}"><span class="en" data="workday" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'workday' ? 'active' : '' }}"><a class="nav-link" href="{{ url('admin/datetime') }}"><span class="en" data="date_time" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'department' ? 'active' : '' }}"><a class="nav-link" href="{{ url('admin/department') }}"><span class="en" data="department" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'position' ? 'active' : '' }}" ><a class="nav-link " href="{{ url('admin/position') }}"><span class="en" data="position" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'timetable' ? 'active' : '' }}"><a class="nav-link" href="{{ url('admin/timetable') }}"><span class="en" data="timetable" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'leavetype' ? 'active' : '' }}"><a class="nav-link" href="{{ url('admin/leavetype') }}"><span class="en" data="leavetype" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'holiday' ? 'active' : '' }}"><a class="nav-link" href="{{ url('admin/holiday') }}"><span class="en" data="holiday" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'notification' ? 'active' : '' }}"><a class="nav-link" href="{{ url('admin/notification') }}"><span class="en" data="notification" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'counter' ? 'active' : '' }}"><a class="nav-link" href="{{ url('admin/counter') }}"><span class="en" data="counter" id="subtitle-family"></span></a></li>
                        <li class="{{ request()->segment(2) == 'counter' ? 'active' : '' }}"><a class="nav-link" href="{{ url('admin/checkins/histories') }}"><span class="en" data="checkin_history" id="subtitle-family"></span></a></li>
                        
                    </ul>
                </li>
                
                @endif
        </ul>
    </aside>
</div>
