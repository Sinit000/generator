@extends('admin.layouts.master')
@section('title', 'Attendance')
@section('head','Attendance Report')

@section('css')
<link rel="stylesheet" href="{{ asset('backend/modules/datatables/datatables.min.css') }}">
<style>
    body {
        font-family: Arial;
        background: #ececec;
    }

    /* Style the tab */
    .tab {
        overflow: hidden;
        border: 1px solid #ccc;
        background-color: #f1f1f1;
    }

    /* Style the buttons inside the tab */
    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
        font-size: 17px;
    }

    /* Change background color of buttons on hover */
    .tab button:hover {
        background-color: #ddd;
    }

    /* Create an active/current tablink class */
    .tab button.active {
        background-color: #ccc;
    }

    /* Style the tab content */
    .tabcontent {
        display: none;
        padding: 6px 12px;
        border: 1px solid #ccc;
        border-top: none;
    }

    #user-table {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #user-table td,
    #user-table th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #user-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #user-table tr:hover {
        background-color: #ddd;
    }

    #user-table th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #34ace0;
        /* background-color: #04AA6D; */
        color: white;
    }


    /* employee */
    #employee-table {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #employee-table td,
    #employee-table th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #employee-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #employee-table tr:hover {
        background-color: #ddd;
    }

    #employee-table th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #34ace0;
        color: white;
    }

    .lds-roller.hidden {
        display: none;
    }

    /* loading */
    .lds-roller {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
        /* margin-left: -4em; */
        top: 250px;
        right: 400px;
        /* margin: 5% auto; */


    }

    .lds-roller div {
        animation: lds-roller 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        transform-origin: 40px 40px;
    }

    .lds-roller div:after {
        content: " ";
        display: block;
        position: absolute;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: black;
        margin: -4px 0 0 -4px;
    }

    .lds-roller div:nth-child(1) {
        animation-delay: -0.036s;
    }

    .lds-roller div:nth-child(1):after {
        top: 63px;
        left: 63px;
    }

    .lds-roller div:nth-child(2) {
        animation-delay: -0.072s;
    }

    .lds-roller div:nth-child(2):after {
        top: 68px;
        left: 56px;
    }

    .lds-roller div:nth-child(3) {
        animation-delay: -0.108s;
    }

    .lds-roller div:nth-child(3):after {
        top: 71px;
        left: 48px;
    }

    .lds-roller div:nth-child(4) {
        animation-delay: -0.144s;
    }

    .lds-roller div:nth-child(4):after {
        top: 72px;
        left: 40px;
    }

    .lds-roller div:nth-child(5) {
        animation-delay: -0.18s;
    }

    .lds-roller div:nth-child(5):after {
        top: 71px;
        left: 32px;
    }

    .lds-roller div:nth-child(6) {
        animation-delay: -0.216s;
    }

    .lds-roller div:nth-child(6):after {
        top: 68px;
        left: 24px;
    }

    .lds-roller div:nth-child(7) {
        animation-delay: -0.252s;
    }

    .lds-roller div:nth-child(7):after {
        top: 63px;
        left: 17px;
    }

    .lds-roller div:nth-child(8) {
        animation-delay: -0.288s;
    }

    .lds-roller div:nth-child(8):after {
        top: 56px;
        left: 12px;
    }

    @keyframes lds-roller {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
@endsection
<div class="modal fade" id="formModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="company-form">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="user_id" id="user_id">
                    <!-- <div class="form-group">
                        <label for="name">Date  </label>
                        <div class="bootstrap-timepicker">
                            <input type="date" class="form-control datepicker" id="to_date" name="to_date">
                        </div>
                    </div> -->
                    <div class="form-group">
                        <label for="on_duty_time">Checkin Time <sup class="text-danger">*</sup></label>
                        <div class="bootstrap-timepicker">
                            <input type="time" class="form-control timepicker" id="checkin_time" name="checkin_time">
                        </div>
                        <div class="invalid-feedback" id="valid-checkin_time"></div>
                    </div>
                    <div class="form-group">
                        <label for="off_duty_time">Checkout Time <sup class="text-danger">*</sup></label>
                        <div class="bootstrap-timepicker">
                            <input type="time" class="form-control timepicker" id="checkout_time" name="checkout_time">
                        </div>

                        <div class="invalid-feedback" id="valid-checkout_time"></div>
                    </div>
                    <div class="form-group">
                        <label for="name">Note </label>
                        <input type="text" class="form-control" id="note" name="note" placeholder="Enter notes..." autocomplete="off">
                        <!-- <div class="invalid-feedback" id="valid-name"></div> -->
                    </div>
                    <!-- <div class="form-group">
                        <label for="name">Early leave </label>
                        <input type="text" class="form-control" id="early_leave" name="early_leave" placeholder="Enter early leave..." autocomplete="off">
                        <div class="invalid-feedback" id="valid-name"></div>
                    </div> -->
                </form>

            </div>
            <div class="modal-footer no-bd">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Close
                </button>
                <button type="button" id="btn-save" class="btn btn-primary">
                    <i class="fas fa-check"></i>
                    Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
@section('content')
<!-- Modal -->


<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <!-- <h1>Attendance</h1> -->
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <!-- <a href="">
                        <i class="fa fa-home"></i>
                        Dashboard
                    </a> -->
                </div>
                <div class="breadcrumb-item">
                    <!-- <i class="fas fa-user"></i> -->
                    <!-- Attendance List -->
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card card-info">
                <div class="card-header">
                    <div class="ml-auto">
                        <a href="{{url('admin/reports')}}" class="btn btn-info ">Back</a>
                        <!-- <div id="loader" class="lds-dual-ring hidden overlay"></div> -->
                        <div class="lds-roller hidden" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 100;" id="loader">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                        <div class="lds-roller hidden" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 100;" id="loading">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div>
                        <!-- <div id="loader1" class="lds-dual-ring hidden overlay"></div> -->
                        <!-- <div class="lv-circles sm lvl-5" data-label="Loading..."> -->
                    </div>
                </div>

                <div class="card-body">
                    <div class="tab">
                        <button class="tablinks" id="defaultOpen" onclick="openCity(event, 'London')">All Employees</button>
                        <button class="tablinks" onclick="openCity(event, 'Paris')">Employee</button>
                        <!-- <button class="tablinks" onclick="openCity(event, 'Tokyo')">Tokyo</button> -->
                    </div>
                    <div id="London" class="tabcontent">
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select name="" class="form-control general  select2" id="filter" style="width: 100%;">
                                        <option value="">Choose DateRang </option>
                                        <option value="1">Today</option>
                                        <option value="2">This week</option>
                                        <option value="3">This month</option>
                                        <option value="5">Last month</option>
                                        <option value="4">This year</option>
                                    </select>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="date" class="form-control" id="startDate" name="demoDate" autocomplete="off" placeholder="Start Date">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="date" class="form-control" id="endDate" name="demoDate" placeholder="End Date" autocomplete="off">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <button class="btn btn-small btn-dark pull-right" style="margin-right:20px;margin-left:20px;" id="btn_export" type="">Export to Excel</button>
                            <button class="btn btn-small btn-info pull-right" id="btn_print" type="">Print</button>

                        </div>

                        <br>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-bordered" id="user-table">
                                <thead class="thead-light">
                                    <tr>

                                        <th width="70" class="text-center">No</th>
                                        <th class="text-center">Date</th>
                                        <th class="text-center">Employee name</th>
                                        <th class="text-center">Timetable</th>
                                        <th class="text-center">Checkin time</th>

                                        <th class="text-center">Late</th>
                                        <th class="text-center">Checkout time</th>

                                        <th class="text-center">Early</th>
                                        <th class="text-center">Total Time</th>
                                        <th class="text-center">Status </th>

                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>

                    <div id="Paris" class="tabcontent">
                        <br>
                        <div class="row">
                            <!-- <div class="col-md-4">
                                <div class="form-group">
                                    <select name="" class="form-control  select2" id="option_status" style="width: 100%;">
                                        <option value="">Choose Status </option>
                                        <option value="all">All</option>
                                        <option value="leave">Leave</option>
                                        <option value="absent">Absent</option>
                                        <option value="present">Attendance</option>
                                    </select>
                                </div>
                            </div> -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select name="" class="form-control  select2" id="em" style="width: 100%;">
                                        <option value="">Choose Empployee </option>
                                        @foreach($data as $r)
                                        <option value="{{$r->id}}">{{$r->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <select name="" class="form-control dateRange select2" id="filter2" style="width: 100%;">
                                        <option value="">Choose DateRang </option>
                                        <option value="1">Today</option>
                                        <option value="2">This week</option>
                                        <option value="3">This month</option>
                                        <option value="5">Last month</option>
                                        <option value="4">This year</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="date" class="form-control" id="startUserDate" name="demoDate" autocomplete="off" placeholder="Start Date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="date" class="form-control" id="endUserDate" name="demoDate" placeholder="End Date" autocomplete="off">
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <button class="btn btn-small btn-dark pull-right" style="margin-right:20px;margin-left:20px;" id="btn_export2" type="">Export to Excel</button>
                            <button class="btn btn-small btn-info pull-right" id="btn_print" type="">Print</button>

                        </div>

                        <br>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-bordered" id="employee-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>
                                            <input type="checkbox" name="main_checkbox"><label></label>
                                        </th>
                                        <th width="70" class="text-center">No</th>
                                        <th class="text-center">Date</th>

                                        <th class="text-center">Timetable</th>
                                        <th class="text-center">Checkin time</th>
                                        <th class="text-center">Checkin status</th>
                                        <th class="text-center">Late</th>
                                        <th class="text-center">Checkout time</th>
                                        <th class="text-center">Checkout status</th>
                                        <th class="text-center">Early</th>
                                        <th class="text-center">Total Time</th>
                                        <th class="text-center">Status <button class="btn btn-sm btn-danger d-none" id="deleteAllBtn"></button></th>
                                        <th class="text-center">Action</th>



                                    </tr>
                                </thead>

                            </table>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('js')
<script src="{{ asset('backend/modules/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('backend/modules/sweetalert/sweetalert.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/js/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/js/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/js/bootstrap-datepicker.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('backend/js/printThis.js')}}"></script>

<script>
    //    
    // Define our button click listener

    function openCity(evt, cityName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(cityName).style.display = "block";
        evt.currentTarget.className += " active";
    }
    document.getElementById("defaultOpen").click();
    $(document).ready(function() {
        // Setup AJAX CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var statDate = null;
        var endDate = null;
        var endUserDate = null;
        var statUserDate = null;
        var userId = null;

        var fromMyDate = "";
        var toMyDate = "";
        $('#startDate').change(function() {
            statDate = $('#startDate').val();
            $('#filter').val('');
            if (endDate == null) {

            } else {
                $('#user-table tbody').remove();
                getData(statDate, endDate);
            }
        });

        $('#endDate').change(function() {
            endDate = $('#endDate').val();
            $('#filter').val('');
            if (statDate == null) {} else {
                $('#user-table tbody').remove();
                getData(statDate, endDate);
            }
        });
        $('.general').change(function() {
            var s1 = $('#filter').find("option:selected");
            $('#startDate').val('');
            $('#endDate').val('');
            var item1 = s1.val();
            statDate = null;
            endDate = null;
            $('#user-table tbody').remove();
            if (item1 == "1") {
                console.log();
                getData('1', '1');

            }
            if (item1 == "2") {
                getData('2', '2');
            }
            if (item1 == "3") {
                getData('3', '3');
            }
            if (item1 == "4") {
                getData('4', '4');
            }
            if (item1 == "5") {
                getData('5', '5');
            }

        });

        function getData(startDate, endDate) {
            fromMyDate = startDate;
            toMyDate = endDate;

            $.ajax({
                type: "GET",
                url: "{{ url('admin/report/attendances') }}" + '/' + fromMyDate + '/' + toMyDate + '/' + 'false',
                dataType: 'json',
                beforeSend: function() {
                    console.log('before send'); // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                },
                success: function(data) {
                    if (data.data.length == 0) {
                        var element = `<tbody>
                                <tr>

                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    
                                </tr>
                                </tbody>
                            `;

                        $('#user-table').append(element);
                    } else {
                        data.data.forEach((item, index) => {
                            var element = `<tbody>
                                <tr>

                                    <td  class="text-center">${index+1}</td>
                                    <td  class="text-center">${item.checkin_date}</td>
                                    <td class="text-center">${item.user_name}</td>
                                    <td class="text-center">${item.timetable_id}</td>
                                    <td  class="text-center">${item.checkin_time}</td>
                                    <td class="text-center">${item.checkin_late}</td>
                                    <td  class="text-center">${item.checkout_time}</td>
                                    <td class="text-center">${item.checkout_late}</td>
                                    <td class="text-center">${item.duration}</td>
                                    <td class="text-center">${item.status}</td>
                                    
                                </tr>
                                </tbody>
                            `;

                            $('#user-table').append(element);
                        });
                    }


                },
                complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loader').addClass('hidden')
                },
            });

        }
        $('#btn_export').on('click', function() {
            if (fromMyDate == "" && toMyDate == "") {
                fromMyDate = "3";
                toMyDate = "3";

            }
            var url = "{{URL::to('admin/report/attendances')}}" + '/' + fromMyDate + '/' + toMyDate + '/' + 'true';
            console.log(url);

            window.location.assign(url)
        });
        // attendance by emplloyee
        $('#btn_print').click(function() {
            $('#user-table').printThis({
                importStyle: true,
                importCSS: true
            });
        });

        var status = "all";

        $('#option_status').change(function() {
            var optionSelected = $('#option_status').find("option:selected");

            var item = optionSelected.val();
            status = item;


        });
        var fromDate = "";
        var toDate = "";
        $('#startUserDate').change(function() {
            statUserDate = $('#startUserDate').val();

            $('#filter2').val('');
            if (userId == null) {

            } else {
                if (endUserDate == null) {

                } else {
                    if (endUserDate.length <= 1) {
                        console.log('cas31')
                        endUserDate = statUserDate;
                    } else {
                        console.log('cas32')
                        $('#employee-table tbody').remove();
                        getEmployeeData(statUserDate, endUserDate, userId);
                    }
                }
            }
        });

        $('#endUserDate').change(function() {
            endUserDate = $('#endUserDate').val();
            $('#filter2').val('');
            if (userId == null) {

            } else {
                if (statUserDate == null) {

                } else {
                    if (statUserDate.length <= 1) {
                        endUserDate = statUserDate;
                    } else {
                        $('#employee-table tbody').remove();
                        getEmployeeData(statUserDate, endUserDate, userId);
                    }
                }
            }
        });

        // filter by employee
        $('#em').change(function() {
            var optionSelected = $('#em').find("option:selected");
            var item = optionSelected.val();
            userId = item;
            if (statUserDate == null && endUserDate == null) {

            } else {
                console.log('case')
                if (statUserDate.length <= 1) {
                    endUserDate = statUserDate;
                }
                $('#employee-table tbody').remove();
                getEmployeeData(statUserDate, endUserDate, userId);
            }
        });
        // filter daterang
        $('.dateRange').change(function() {
            var selected = $('#filter2').find("option:selected");
            // console.log('user_id');
            // console.log(userId);
            $('#startUserDate').val("");
            $('#endUserDate').val("");
            statUserDate = null;
            endUserDate = null;
            var myitem = selected.val();

            $('#employee-table tbody').remove();

            if (userId == null) {
                if (myitem == "1") {
                    statUserDate = "1";
                }
                if (myitem == "2") {
                    statUserDate = "2";
                }
                if (myitem == "3") {
                    statUserDate = "3";
                }
                if (myitem == "4") {
                    statUserDate = "4";
                }
                if (myitem == "5") {
                    statUserDate = "5";
                }
            } else {

                if (myitem == "1") {
                    statUserDate = "1";
                    getEmployeeData('1', '1', userId);


                }
                if (myitem == "2") {
                    statUserDate = "2";
                    getEmployeeData('2', '2', userId);

                }
                if (myitem == "3") {
                    statUserDate = "3";
                    getEmployeeData('3', '3', userId);

                }
                if (myitem == "4") {
                    statUserDate = "4";
                    getEmployeeData('4', '4', userId);

                }
                if (myitem == "5") {
                    statUserDate = "5";
                    getEmployeeData('5', '5', userId);

                }
            }



        });




        function getEmployeeData(statUserDate, endUserDate, userId) {
            fromDate = statUserDate;
            toDate = endUserDate;
            console.log('get')
            // console.log(statUserDate);
            // console.log(endUserDate);
            $.ajax({
                type: "GET",
                url: "{{ url('admin/report/attendance/employee') }}" + '/' + userId + '/' + fromDate + '/' + toDate + '/' + 'false',
                dataType: 'json',
                beforeSend: function() {
                    console.log('before send'); // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loading').removeClass('hidden')
                },
                success: function(data) {
                    // ajax response return data , so must data.data to get the value
                    // console.log(data.data);
                    if (data.data.length == 0) {
                        var element = `<tbody>
                                <tr>

                                    <td  class="text-center"></td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    <td  class="text-center">#</td>
                                    
                                </tr>
                                </tbody>
                            `;
                            $('#employee-table').append(element);

                    } else {
                        data.data.forEach((item, index) => {
                            if (item.send_status == "true") {
                                var element = `<tbody>
                                    <tr>
                                        <td  class="text-center"></td>
                                        <td  class="text-center">${index+1}</td>
                                        <td  class="text-center">${item.checkin_date}</td>
                                    
                                        <td class="text-center">${item.timetable_id}</td>
                                        <td  class="text-center">${item.checkin_time}</td>
                                        <td  class="text-center">${item.checkin_status}</td>
                                        <td class="text-center">${item.checkin_late}</td>
                                        <td  class="text-center">${item.checkout_time}</td>
                                        <td  class="text-center">${item.checkout_status}</td>
                                        <td class="text-center">${item.checkout_late}</td>
                                        <td class="text-center">${item.duration}</td>
                                        <td class="text-center">${item.status}</td>
                                        <td  class="text-center"></td>
                                        
                                    </tr>
                                    </tbody>
                                `;
                            } else {
                                var element = `<tbody>
                                    <tr>
                                    <td  class="text-center"><input type="checkbox" name="country_checkbox" data-id="${item.checkin_id}"></td>
                                        <td  class="text-center">${index+1}</td>
                                        <td  class="text-center">${item.checkin_date}</td>
                                    
                                        <td class="text-center">${item.timetable_id}</td>
                                        <td  class="text-center">${item.checkin_time}</td>
                                        <td  class="text-center">${item.checkin_status}</td>
                                        <td class="text-center">${item.checkin_late}</td>
                                        <td  class="text-center">${item.checkout_time}</td>
                                        <td  class="text-center">${item.checkout_status}</td>
                                        <td class="text-center">${item.checkout_late}</td>
                                        <td class="text-center">${item.duration}</td>
                                        <td class="text-center">${item.status}</td>
                                        <td><button type="button" class="btn btn-success" data-one="${item.user_id}" data-two="${item.checkin_time}" data-three="${item.checkout_time}"    id="confirmBtn" data-id="${item.checkin_id}">Edit</button></td> 
                                        
                                    </tr>
                                    </tbody>
                                `;
                            }


                            $('#employee-table').append(element);
                        });
                    }

                },
                complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loading').addClass('hidden')
                },
            });


        }
        $('#btn_print').click(function() {
            $('#employee-table').printThis({
                importStyle: true,
                importCSS: true
            });
        });

        // btn_export2
        $('#btn_export2').on('click', function() {

            if (fromDate == "" && toDate == "") {
                fromDate = "3";
                toDate = "3";

            }
            var url = "{{URL::to('admin/report/attendance/employee')}}" + '/' + userId + '/' + fromDate + '/' + toDate + '/' + 'true';
            console.log(url);

            window.location.assign(url)
        });
        $('input[name="country_checkbox"]').each(function() {
            this.checked = false;
        });

        // 
        $(document).on('click', 'input[name="main_checkbox"]', function() {
            if (this.checked) {
                $('input[name="country_checkbox"]').each(function() {
                    this.checked = true;
                });
            } else {
                $('input[name="country_checkbox"]').each(function() {
                    this.checked = false;
                });
            }
            console.log('show btn delete all');
            toggledeleteAllBtn();
        });

        $(document).on('change', 'input[name="country_checkbox"]', function() {

            if ($('input[name="country_checkbox"]').length == $('input[name="country_checkbox"]:checked').length) {
                $('input[name="main_checkbox"]').prop('checked', true);
            } else {
                $('input[name="main_checkbox"]').prop('checked', false);
            }
            //    remove btn delete all
            console.log('remove btn remove');
            toggledeleteAllBtn();
        });

        //    if click on btn delete
        function toggledeleteAllBtn() {
            if ($('input[name="country_checkbox"]:checked').length > 0) {
                $('button#deleteAllBtn').text('Submit (' + $('input[name="country_checkbox"]:checked').length + ')').removeClass('d-none');
            } else {
                // new classs create in button delete
                $('button#deleteAllBtn').addClass('d-none');
            }
        }
        // update all
        $(document).on('click', 'button#deleteAllBtn', function() {
            var checkedCountries = [];
            $('input[name="country_checkbox"]:checked').each(function() {
                checkedCountries.push($(this).data('id'));
            });


            var url = '{{ url("admin/attendance/updates") }}' + "/" + userId + "/" + fromDate + "/" + toDate;
            if (checkedCountries.length > 0) {
                console.log('testing');
                $.ajax({
                    type: "POST",
                    url: url,
                    data: {
                        countries_ids: checkedCountries
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        console.log('before send'); // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                        $('#loading').removeClass('hidden')
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.code == 0) {


                            swal({
                                title: "Success!",
                                text: "Data has been send  successfully!",
                                icon: "success",
                                timer: 3000
                            });
                        } else {
                            swal({
                                title: "Sorry",
                                text: data.message,
                                icon: "info",
                                timer: 3000
                            });
                        }
                    },
                    complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.
                        $('#loading').addClass('hidden');
                        $('#employee-table tbody').remove();
                        $('button#deleteAllBtn').addClass('d-none');
                        $('input[name="main_checkbox"]').prop('checked', false);
                        getEmployeeData(fromDate, toDate, userId)
                    },
                });

            }
        });
        // edit checkin by employee
        $(document).on('click', '#confirmBtn', function() {

            var checkin_id = $(this).data('id');
            var userId = $(this).data('one');
            var checkin_time = $(this).data('two');
            var checkout_time = $(this).data('three');
            $('#checkin_time').val(checkin_time);
            $('#checkout_time').val(checkout_time);

            $('#id').val(checkin_id);
            $('#user_id').val(userId);
            $('#formModal').modal('show');
            $('.modal-title').html('Edit Data');
            $('.close').click(function() {
                $('#checkin_time').val();
                $('#checkout_time').val();
                $('#id').val();
                $('#user_id').val();
                console.log('clear value');
            })
            $('#btn-close').click(function() {
                // remove that from select value after save data to avoid dublicate data
                $('#checkin_time').val();
                $('#checkout_time').val();
                $('#id').val();
                $('#user_id').val();
                console.log('clear value');
            })



        });
        $(document).on('click', '#btn-save', function() {
            $checkinId = $('#id').val();
            var fromdata = {
                user_id: $('#user_id').val(),
                checkin_time: $('#checkin_time').val(),
                checkout_time: $('#checkout_time').val(),
                note: $('#note').val()
            };
            $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Updating...').attr("disabled", true);
            // console.log(fromdata);

            $.ajax({
                type: "PUT",
                url: "{{url('admin/checkins/update')}}" + "/" + $checkinId,
                data: fromdata,
                dataType: 'json',
                beforeSend: function() {
                    console.log('before send'); // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    // $('#loading').removeClass('hidden')
                },
                success: function(data) {
                    console.log(data);
                    if (data.code == 0) {


                        swal({
                            title: "Success!",
                            text: data.message,
                            icon: "success",
                            timer: 3000
                        });
                    } else {
                        swal({
                            title: "Sorry",
                            text: data.message,
                            icon: "info",
                            timer: 3000
                        });
                    }
                    $('#formModal').modal('hide');
                    // $('#btn-save').removeAttr('disabled');
                    $('#btn-save').html('<i class="fas fa-check"></i> Save Changes');
                    $('#btn-save').removeAttr('disabled');
                },
                complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    // $('#loading').addClass('hidden')
                    console.log(fromdata['user_id']);
                    $('#employee-table tbody').remove();
                    getEmployeeData(fromDate, toDate, fromdata['user_id']);
                },
            });
        });

    });
</script>
@endsection