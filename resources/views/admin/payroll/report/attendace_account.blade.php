@extends('admin.layouts.master')
@section('title', 'Report')
@section('head','Attendance Report')

@section('css')
<link rel="stylesheet" href="{{ asset('backend/modules/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('backend/css/bootstrap-datepicker.standalone.min.css') }}">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/1.0.5/datepicker.min.css" integrity="sha512-OuupWckAJJIcRPiQcajus5jyV6v8skGpZ+9LUETpclmq5a2eph8nspQO0u9an5firIwX6SF2jOV3YgoHWIO7+Q==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
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

    /*  */
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
                    <div class="form-group">
                        <label for="nip">Deduction </label>
                        <input type="text" class="form-control" id="deduction" name="deduction" placeholder="Enter deduction..." autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="nip">Bonus </label>
                        <input type="text" class="form-control" id="bonus" name="bonus" placeholder="Enter bonus..." autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="nip">Advance Money </label>
                        <input type="text" class="form-control" id="advance_salary" name="advance_salary" placeholder="Enter advance_salary..." autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label for="nip">Notes </label>
                        <input type="text" class="form-control" id="notes" name="notes" placeholder="Enter advance_salary..." autocomplete="off">
                    </div>

                </form>

            </div>
            <div class="modal-footer no-bd">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btn-close">
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
            <!-- <h1>Attendance Report</h1> -->
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                   
                </div>
                <div class="breadcrumb-item">
                    <!-- <i class="fas fa-user"></i>
                    attendance List -->
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card card-info">
                <div class="card-header">
                    <div class="card-header">

                        <div class="ml-auto">
                            <a href="{{url('accountant/payslip')}}" class="btn btn-info ">Payslip</a>
                        </div>
                    </div>
                    <div class="ml-auto">
                        <!-- <a href="{{url('accountant/reports')}}" class="btn btn-info ">Back</a> -->
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
                        <!-- <div class="lds-facebook hidden" id="loader"><div></div><div></div><div></div></div> -->

                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <select name="" class="form-control  select2" id="filter" style="width: 100%;">
                                    <option value="">Choose Value </option>
                                    <!-- <option value="1">Today</option> -->
                                    <!-- <option value="2">This week</option> -->
                                    <option value="3">This month</option>
                                    <option value="5">Last month</option>
                                    <!-- <option value="4">This year</option> -->
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
                                    <!-- <th><input type="checkbox" name="main_checkbox"><label></label></th> -->
                                    <th width="70" class="text-center">No</th>
                                    <th class="text-center">Name</th>
                                    <th class="text-center">Position</th>
                                    <th class="text-center">Total Attendance</th>
                                    <th class="text-center">Total Deduction</th>
                                    <th>Action<button class="btn btn-sm btn-danger d-none" id="deleteAllBtn"></button></th>


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
<script type="text/javascript" src="{{ asset('backend/bootstrap-datepicker.min.js') }}"></script>
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
        // var dataList = [];
        var idUser = "";
        var total_attendance = 0;
        var fromDate = "";
        var toDate = "";



        // $('#startDate').datepicker({
        //     format: "dd-mm-yyyy",
        //     autoclose: true,
        //     todayHighlight: true
        // });



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

            if (statDate == null) {

            } else {
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
            // console.log('get data');
            fromDate = startDate;
            toDate = endDate;
            const x = new Date();
            // let thisMonth = d.getMonth();
            var thisMonth = (x.getMonth() + 1).toString();
            console.log(month);
            var lastMonth="";
            // this month           
            if(fromDate==3){
                thisMonth= thisMonth;
                console.log(thisMonth);
            }
            if(toDate==5){
                lastMonth= thisMonth-1;
                console.log(lastMonth);
            }
             var month_input = fromDate.split("-");
            $.ajax({
                type: "GET",
                url: "{{ url('accountant/report/attendances/get') }}" + '/' + startDate + '/' + endDate + '/' + 'false' + '/' + 'false',
                dataType: 'json',
                beforeSend: function() {
                    console.log('before send'); // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    // $('#loader').removeClass('hidden');
                    $('#loader').removeClass('hidden');


                },
                success: function(data) {
                    
                    // ajax response return data , so must data.data to get the value
                    if(data.data.length ==0){
                        var element = `<tbody>
                                <tr>
                                   
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
                    }else{
                         // console.log(data);
                    data.data.forEach((item, index) => {
                         if(month_input[1]==item.month || thisMonth== item.month || lastMonth==item.month){
                            var element = `<tbody>
                                <tr>
                                   
                                    <td  class="text-center">${index+1}</td>
                                    <td  class="text-center">${item.user_name}</td>
                                    <td class="text-center">${item.position_name}</td>
                                    <td class="text-center">${item.new_attendance}</td>
                                    <td  class="text-center">${item.leave_deduction}</td>
                                     <td>  
                                    </td> 
                                </tr>
                                </tbody>
                            `;
                        }else{
                            var element = `<tbody>
                                <tr>
                                
                                    <td  class="text-center">${index+1}</td>
                                    <td  class="text-center">${item.user_name}</td>
                                    <td class="text-center">${item.position_name}</td>
                                    <td class="text-center">${item.new_attendance}</td>
                                    <td  class="text-center">${item.leave_deduction}</td>
                                     <td> 
                                    <button type="button" class="btn btn-success" data-one="${item.new_attendance}" data-two="${item.leave_deduction}" id="confirmBtn" data-id="${item.user_id}">Confirm</button> <button type="button" class="btn btn-info"  id="editBtn" data-one="${item.total_checkin}" data-two="${item.leave_deduction}" data-id="${item.user_id}">Edit</button></td> 
                                </tr>
                                </tbody>
                            `;
                        }




                        $('#user-table').append(element);
                    });
                    }
                   
                },
                complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loader').addClass('hidden')
                },
            });

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
            var url = "{{URL::to('accountant/report/attendances/get')}}" + '/' + fromDate + '/' + toDate + '/' + 'true' + '/' + 'true';
            console.log(url);
            window.location.assign(url)
        });
        // confirm
        $(document).on('click', '#confirmBtn', function() {
            idUser = $(this).attr('data-id');

            console.log(idUser);
            total = $(this).attr('data-one');
            leave = $(this).attr('data-two');
            var formatStart = $('#startDate').val();
            var formatEnd = $('#endDate').val();
            var formDateRange = $('#filter').val();


            if (formDateRange == '') {

            } else {
                formatStart = formDateRange;
                formatEnd = formDateRange;
            }
            console.log(formatStart);
            console.log(formatEnd);
            console.log('total attendance');

            var formData = {
                checkin: total,
                leave_deduction: leave,
                startDate: formatStart,
                endDate: formatEnd,
                // user_id:idUser

            };

            $.ajax({
                type: "POST",
                url: "{{url('accountant/payslip/store')}}" + '/' + idUser,
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    console.log('before send'); // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                },
                success: function(data) {
                    console.log(data);
                    if (data.code == 0) {
                        swal({
                            title: "Success!",
                            text: "Data has been added successfully!",
                            icon: "success",
                            timer: 2000
                        });
                    } else {
                        swal({
                            title: "Sorry!",
                            text: data.message,
                            icon: "error",
                            timer: 3000
                        });
                    }


                    $('#user-table').DataTable().draw(false);
                    $('#user-table').DataTable().on('draw', function() {
                        $('[data-toggle="tooltip"]').tooltip();
                    });

                    $('#formModal').modal('hide');
                },
                error: function(data) {
                    try {
                        swal({
                            title: "Sorry!",
                            text: "An error occurred, please try again",
                            icon: "error",
                            timer: 3000
                        });
                    } catch {
                        swal({
                            title: "Sorry!",
                            text: "An error occurred, please try again",
                            icon: "error",
                            timer: 3000
                        });

                        $('#formModal').modal('hide');
                    }
                },
                complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loader').addClass('hidden');
                    $('#user-table tbody').remove();
                    getData(fromDate, toDate);
                },
            });
        });
        // 
        $('input[name="country_checkbox"]').each(function() {
            this.checked = false;
        });

        // 
        // $(document).on('click', 'input[name="main_checkbox"]', function() {
        //     if (this.checked) {
        //         $('input[name="country_checkbox"]').each(function() {
        //             this.checked = true;
        //         });
        //     } else {
        //         $('input[name="country_checkbox"]').each(function() {
        //             this.checked = false;
        //         });
        //     }
        //     console.log('show btn delete all');
        //     toggledeleteAllBtn();
        // });

        // $(document).on('change', 'input[name="country_checkbox"]', function() {

        //     if ($('input[name="country_checkbox"]').length == $('input[name="country_checkbox"]:checked').length) {
        //         $('input[name="main_checkbox"]').prop('checked', true);
        //         var data = [];
        //         var total = $(this).attr('data-five');
        //         var leave = $(this).attr('data-six');
        //         var user_id = $(this).attr('data-four');
        //         var formatStart = $('#startDate').val();
        //         var formatEnd = $('#endDate').val();
        //         var formDateRange = $('#filter').val();

        //         if (formDateRange == '') {

        //         } else {
        //             formatStart = formDateRange;
        //             formatEnd = formDateRange;
        //         }
        //         console.log(total);
        //         console.log(leave);
        //         console.log(user_id);
        //         console.log(formatStart);
        //         console.log(formatEnd);


        //         // var newForm = {
        //         //     checkin: total,
        //         //     leave_deduction: leave,
        //         //     startDate: formatStart,
        //         //     endDate: formatEnd,
        //         //     user_id: user_id

        //         // };

        //     } else {
        //         $('input[name="main_checkbox"]').prop('checked', false);
        //     }
        //     // console.log(idUser);

        //     //    remove btn delete all
        //     console.log('remove btn remove');
        //     // console.log(newForm);
        //     toggledeleteAllBtn();
        // });

        //    if click on btn delete
        // function toggledeleteAllBtn() {
        //     if ($('input[name="country_checkbox"]:checked').length > 0) {
        //         $('button#deleteAllBtn').text('Submit (' + $('input[name="country_checkbox"]:checked').length + ')').removeClass('d-none');
        //     } else {
        //         // new classs create in button delete
        //         $('button#deleteAllBtn').addClass('d-none');
        //     }
        // }
        // // update all
        // $(document).on('click', 'button#deleteAllBtn', function() {
        //     var checkAllss = [];
        //     var data = [];


        //     var total = $(this).attr('data-five');
        //     var leave = $(this).attr('data-six');
        //     var user_id = $(this).attr('data-four');
        //     var formatStart = $('#startDate').val();
        //     var formatEnd = $('#endDate').val();
        //     var formDateRange = $('#filter').val();


        //     if (formDateRange == '') {

        //     } else {
        //         formatStart = formDateRange;
        //         formatEnd = formDateRange;
        //     }


        //     var newForm = {
        //         checkin: total,
        //         leave_deduction: leave,
        //         startDate: formatStart,
        //         endDate: formatEnd,
        //         user_id: user_id

        //     };

        //     $('input[name="country_checkbox"]:checked').each(function() {
        //         checkAllss.push(newForm);
        //     });

        //     console.log(newForm);
        //     var url = '{{ url("accountant/payslip/add") }}';
        //     // $.ajax({
        //     //     type: "POST",
        //     //     url: url,
        //     //     data: {
        //     //         countries_ids: checkAllss
        //     //     },
        //     //     dataType: 'json',
        //     //     beforeSend: function() {

        //     //     },
        //     //     success: function(data) {
        //     //         console.log(data);



        //     //     },
        //     //     error: function(data) {

        //     //     },
        //     //     complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.

        //     //     },
        //     // });

        // });



        var totalAttendance = 0;
        var leaveDeduction = 0;
        $(document).on('click', '#editBtn', function() {
            idUser = $(this).attr('data-id');
            totalAttendance = $(this).attr('data-one');
            leaveDeduction = $(this).attr('data-two');

            $('#formModal').modal('show');
            $('.modal-title').html('Edit Data');
        });
        // insert into payroll
        $('#btn-save').click(function() {

            console.log('total attendance');
            console.log(totalAttendance);
            console.log(leaveDeduction);
            var formatStart = $('#startDate').val();
            var formatEnd = $('#endDate').val();
            var formDateRange = $('#filter').val();
            console.log('total attendance');

            if (formDateRange == '') {

            } else {
                formatStart = formDateRange;
                formatEnd = formDateRange;
            }
            // var deduct = $('#deduction').val();
            // var totalDeduction =  deduct + leaveDeduction;

            var formData = {
                deduction: $('#deduction').val(),
                bonus: $('#bonus').val(),
                advance_salary: $('#advance_salary').val(),
                notes: $('#notes').val(),
                checkin: totalAttendance,
                leave_deduction: leaveDeduction,
                startDate: formatStart,
                endDate: formatEnd


            };

            $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Saving...').attr("disabled", true);
            $.ajax({
                type: "POST",
                url: "{{url('accountant/payslip/store')}}" + '/' + idUser,
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    // console.log('before send'); // Before we send the request, remove the .hidden class from the spinner and default to inline-block.
                    $('#loader').removeClass('hidden')
                },
                success: function(data) {
                    console.log(data);
                    if (data.code == 0) {
                        swal({
                            title: "Success!",
                            text: "Data has been added successfully!",
                            icon: "success",
                            timer: 2000
                        });
                    } else {
                        swal({
                            title: "Sorry!",
                            text: data.message,
                            icon: "error",
                            timer: 3000
                        });
                    }


                    $('#user-table').DataTable().draw(false);
                    $('#user-table').DataTable().on('draw', function() {
                        $('[data-toggle="tooltip"]').tooltip();
                    });

                    $('#formModal').modal('hide');
                },
                error: function(data) {
                    try {
                        swal({
                            title: "Sorry!",
                            text: "An error occurred, please try again",
                            icon: "error",
                            timer: 3000
                        });
                    } catch {
                        swal({
                            title: "Sorry!",
                            text: "An error occurred, please try again",
                            icon: "error",
                            timer: 3000
                        });

                        $('#formModal').modal('hide');
                    }
                },
                complete: function() { // Set our complete callback, adding the .hidden class and hiding the spinner.
                    $('#loader').addClass('hidden');
                    $('#user-table tbody').remove();
                    getData(fromDate, toDate);
                },
            });



        });
        $('.close').click(function() {

            // console.log('close button');
            $('#company-form').trigger('reset');
        })
        $('#btn-close').click(function() {

            // remove that from select value after save data to avoid dublicate data
            $('#company-form').trigger('reset');
        })

    });


    // 
</script>
@endsection