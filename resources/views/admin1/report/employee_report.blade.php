@extends('admin.layouts.master')
@section('title', 'Report')
@section('head','Employee Report')

@section('css')
<link rel="stylesheet" href="{{ asset('backend/modules/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/bootstrap-datepicker.standalone.min.css') }}">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.0/css/bootstrap-datepicker.standalone.min.css" integrity="sha512-ZgpiugtcWdV2LX1a1uy6ckVtJ8J3W7VBgYpKzyqmJ0UFJef1biYdOlOM2hl35gkf9ki56WoUeDQlIRqgdUhKOQ==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
<style>
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
        color: white;
    }

    /*Hidden class for adding and removing*/
    /*Hidden class for adding and removing*/
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

@section('content')
<!-- Modal -->
@php
$fildter_data= [
'item'=> ['this week',1],
'item'=> ['this month',2],
'item'=> ['this year',3],

]
@endphp

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <!-- <h1>Employee Report</h1> -->
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <!-- <a href="{{ url('admin/dashboard') }}">
                        <i class="fa fa-home"></i>
                        Dashboard
                    </a> -->
                </div>
                <div class="breadcrumb-item">
                    <!-- <i class="fas fa-user"></i>
                    Employee List -->
                </div>
            </div>
        </div>
        <div class="section-body">

            <div class="card card-info">
                <div class="card-header">
                    <div class="ml-auto">
                        <a href="{{url('admin/reports')}}" class="btn btn-info ">Back</a>
                        <!-- <div id="loader" class="lds-dual-ring hidden overlay"></div> -->
                        <!-- <div class="lds-roller hidden" id="loader">
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                            <div></div>
                        </div> -->


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

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <select name="" class="form-control  select2" id="filter" style="width: 100%;">
                                    <option value="">Choose Value </option>
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
                                    <th>
                                        <input type="checkbox" name="main_checkbox"><label></label>
                                    </th>
                                    <th width="70" class="text-center">No</th>
                                    <th class="text-center">Join Date</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Gender</th>
                                    <th class="text-center">Phone</th>

                                    <th class="text-center">Department</th>
                                    <th class="text-center">Position</th>
                                    <th class="text-center">Type</th>
                                    <th class="text-center">Start Date</th>
                                    <th class="text-center">Working Time</th>
                                    <th class="text-center">Base Salary <button class="btn btn-sm btn-danger d-none" id="deleteAllBtn"></button></th>
                                </tr>
                            </thead>

                        </table>
                    </div>

                    <!--  -->
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
<script type="text/javascript">
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var statDate = null;
        var endDate = null;
        var dataList = [];
        var fromDate = "";
        var toDate = "";



        $('.startDate').datepicker({
            format: "dd-mm-yyyy",
            autoclose: true,
            todayHighlight: true
        });



        // get data
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
        $('select').change(function() {
            var s = $('#filter').find("option:selected");
            var item = s.val();
            $('#startDate').val('');
            $('#endDate').val('');
            statDate = null;
            endDate = null;
            $('#user-table tbody').remove();
            if (item == "1") {
                console.log();
                getData('1', '1');

            }
            if (item == "2") {
                getData('2', '2');
            }
            if (item == "3") {
                getData('3', '3');
            }
            if (item == "4") {
                getData('4', '4');
            }
            if (item == "5") {
                getData('5', '5');
            }

        });




        function getData(startDate, endDate) {
            // get data
            fromDate = startDate;
            toDate = endDate;

            $.ajax({
                type: "GET",
                url: "{{ url('admin/report/employee') }}" + '/' + startDate + '/' + endDate + '/' + "false",
                dataType: 'json',
                beforeSend: function() {
                    console.log('before send'); // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                },
                success: function(data) {
                    console.log(data);
                    if (data.data.length == 0) {
                        var element = `<tbody>
                                    <tr>
                                        <td  class="text-center"></td>
                                        <td  class="text-center">#</td>
                                        <td  class="text-center">#</td>
                                        <td class="text-center">#</td>
                                        <td class="text-center">#</td>
                                        <td  class="text-center">#</td>
                                        
                                        <td  class="text-center">#</td>
                                        <td class="text-center">#</td>
                                        <td class="text-center">#</td>
                                        <td class="text-center">#</td>
                                        <td class="text-center">#</td>
                                        <td class="text-center">#</td>
                                    </tr>
                                </tbody>
                                `;
                                $('#user-table').append(element);
                    } else {
                        data.data.forEach((item, index) => {
                            if (item.status == "false") {
                                if (item.base_salary == null) {

                                    var element = `<tbody>
                                <tr>
                                    <td  class="text-center"><input type="checkbox" name="country_checkbox" data-id="${item.id}"></td>
                                    <td  class="text-center">${index+1}</td>
                                    <td  class="text-center">${item.customer_date}</td>
                                    <td class="text-center">${item.name}</td>
                                    <td class="text-center">${item.gender}</td>
                                    <td  class="text-center">${item.employee_phone}</td>
                                    
                                    <td  class="text-center">${item.department_name}</td>
                                    <td class="text-center">${item.position_name}</td>
                                    <td class="text-center">${item.position_type}</td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                </tr>
                                </tbody>
                            `;
                                } else {
                                    var element = `<tbody>
                                <tr>
                                <td  class="text-center"><input type="checkbox" name="country_checkbox" data-id="${item.id}"></td>
                                    <td  class="text-center">${index+1}</td>
                                    <td  class="text-center">${item.customer_date}</td>
                                    <td class="text-center">${item.name}</td>
                                    <td class="text-center">${item.gender}</td>
                                    <td  class="text-center">${item.employee_phone}</td>
                                    
                                    <td  class="text-center">${item.department_name}</td>
                                    <td class="text-center">${item.position_name}</td>
                                    <td class="text-center">${item.position_type}</td>
                                    <td class="text-center">${item.start_date}</td>
                                    <td class="text-center">${item.working_schedule}</td>
                                    <td class="text-center">$${item.base_salary}</td>
                                </tr>
                                </tbody>
                            `;
                                }
                            } else {
                                if (item.base_salary == null) {

                                    var element = `<tbody>
                                    <tr>
                                        <td  class="text-center"></td>
                                        <td  class="text-center">${index+1}</td>
                                        <td  class="text-center">${item.customer_date}</td>
                                        <td class="text-center">${item.name}</td>
                                        <td class="text-center">${item.gender}</td>
                                        <td  class="text-center">${item.employee_phone}</td>
                                        
                                        <td  class="text-center">${item.department_name}</td>
                                        <td class="text-center">${item.position_name}</td>
                                        <td class="text-center">${item.position_type}</td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                        <td class="text-center"></td>
                                    </tr>
                                    </tbody>
                                    `;
                                } else {
                                    var element = `<tbody>
                                    <tr>
                                        <td  class="text-center"></td>
                                        <td  class="text-center">${index+1}</td>
                                        <td  class="text-center">${item.customer_date}</td>
                                        <td class="text-center">${item.name}</td>
                                        <td class="text-center">${item.gender}</td>
                                        <td  class="text-center">${item.employee_phone}</td>
                                        
                                        <td  class="text-center">${item.department_name}</td>
                                        <td class="text-center">${item.position_name}</td>
                                        <td class="text-center">${item.position_type}</td>
                                        <td class="text-center">${item.start_date}</td>
                                        <td class="text-center">${item.working_schedule}</td>
                                        <td class="text-center">$${item.base_salary}</td>
                                    </tr>
                                </tbody>
                                `;
                                }
                            }



                            $('#user-table').append(element);
                        });
                    }





                },
                complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loader').addClass('hidden')
                },
            });

            // ajax get data

        }
        $('#btn_print').click(function() {
            $('#user-table').printThis({
                importStyle: true,
                importCSS: true
            });
        });

        $('#btn_export').on('click', function() {
            if (fromDate == "" && toDate == "") {
                fromDate = "3";
                toDate = "3";
            }
            var url = "{{URL::to('admin/report/employee')}}" + '/' + fromDate + '/' + toDate + '/' + "true";
            console.log(url);

            window.location.assign(url)


        });
        $('input[name="country_checkbox"]').each(function() {
            this.checked = false;
        });
        $('input[name="main_checkbox"]').prop('checked', false);
        $('button#deleteAllBtn').addClass('d-none');
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

            var url = '{{ url("admin/employee/updates") }}';
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
                        $('#loader').removeClass('hidden')
                    },
                    success: function(data) {
                        console.log(data);
                        if (data.code == 0) {
                            // $('#user-table').DataTable().draw(false);
                            // $('#user-table').DataTable().on('draw', function() {
                            //     $('[data-toggle="tooltip"]').tooltip();
                            // });

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
                        $('#loader').addClass('hidden');
                        $('#user-table tbody').remove();
                        $('button#deleteAllBtn').addClass('d-none');
                        $('input[name="main_checkbox"]').prop('checked', false);
                        getData(fromDate, toDate);
                    },
                });
                // console.log(checkedCountries[1]);
                // $.post(url, {
                //     countries_ids: checkedCountries
                // }, function(data) {
                //     console.log(data);

                // }, 'json');

            }
        });

    });
</script>
@endsection