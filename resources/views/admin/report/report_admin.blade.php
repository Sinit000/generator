@extends('admin.layouts.master')
@section('title', 'Dashboard')
@section('head','')
@section('css')
<!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/modules/fullcalendar/fullcalendar.min.css"> -->

<style>
    #title-head{
        font-size:16px;
    }
    .en[data="dashboard"]::before {
        content: 'Dashboard'

    }

    .kh[data="dashboard"]::before {
        content: 'ផ្ទាំងការងារ'
    }
    .en[data="report"]::before {
        content: 'Report'

    }

    .kh[data="report"]::before {
        content: 'របាយការណ៌'
    }
    .en[data="employee_report"]::before {
        content: 'Employee Report'

    }

    .kh[data="employee_report"]::before {
        content: 'របាយការណ៌បុគ្គលិក'
    }
    .en[data="att_report"]::before {
        content: 'Attendance Report'

    }

    .kh[data="att_report"]::before {
        content: 'របាយការណ៌វត្តមាន'
    }
    .en[data="overtime_report"]::before {
        content: 'Overtime Report'

    }

    .kh[data="overtime_report"]::before {
        content: 'របាយការណ៌ថែមម៉ោង'
    }
    .en[data="leave_report"]::before {
        content: 'Leave Report'

    }

    .kh[data="leave_report"]::before {
        content: 'របាយការណ៌ច្បាប់'
    }
</style>

@endsection

@section('content')
<!-- Main Content -->
<div class="main-content">
    <section class="section ">
        <div class="section-header">
            <h1 data="report" id="title-family" class="en"></h1>
            <div class="section-header-breadcrumb">
               <div class="breadcrumb-item" >
                    <i class="fa fa-home" ></i>
                    <a href="{{ url('admin/dashboard') }}" data="dashboard" id="subtitle-family" class="en" ></a>
                </div>
            </div>
        </div>
        <!--<div class="section-header pt-80 border-0" style="background-color: transparent;">-->
        <!--     <h1 data="report" id="title-family" class="en"></h1>-->
        <!--    <div class="section-header-breadcrumb">-->
        <!--       <div class="breadcrumb-item" >-->
        <!--            <i class="fa fa-home" ></i>-->
        <!--            <a href="{{ url('admin/dashboard') }}" data="dashboard" id="subtitle-family" class="en" ></a>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <!-- <div class="card">
                    <div class="card-header">
                        <h4>Welcome to Attendance App</h4>
                    </div>

                    </div> -->
                    <div class="row" >
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-primary">
                                    <i class="far fa-user" style="font-size:20px;color:white;"></i>
                                </div>
                                
                                    
                                
                                <div class="card-wrap" id="employee_report">
                                        <div class="card-header">
                                           <a href="reports/employee"> <h1  id="title-head" data="employee_report" id="title-family" class="en"></h1></a>
                                        </div>
                                        <div class="card-body">
                                            
                                        </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-primary">
                                    <i class="fas fa-check-circle" style="color:white;"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <a href="reports/attendances"><h1 id="title-head" data="att_report" id="title-family" class="en"></h1></a>
                                    </div>
                                    <div class="card-body">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-primary">
                                    <i class="fas fa-clock fa-xs" style="color:white;"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <a href="reports/overtimes"><h1  id="title-head" data="overtime_report" id="title-family" class="en"></h1></a>
                                    </div>
                                    <div class="card-body">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-primary">
                                    <i class="fa fa-book" style="font-size:20px;color:white;"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <a href="report/leaves"><h1  id="title-head" data="leave_report" id="title-family" class="en"></h4></a>
                                    </div>
                                    <div class="card-body">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-primary">
                                    <i class="far fa-user"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Absent</h4>
                                    </div>
                                    <div class="card-body">
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-warning">
                                    <i class="far fa-newspaper"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4>Leave</h4>
                                    </div>
                                    <div class="card-body">
                                       
                                    </div>
                                </div>
                            </div>
                        </div> -->
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

<script>
    checkLanguage();

    function checkLanguage() {
        langCode = localStorage.getItem("lang-code");
        if (!langCode) {
            langCode = "en";
            localStorage.setItem("lang-code", "en")
        }
        let needTranslates = [];
        if (langCode === "en") {
            needTranslates = [...document.getElementsByClassName('kh')];
            for (let n of needTranslates) {
                n.classList.replace("kh", "en")
            }
        } else {
            needTranslates = [...document.getElementsByClassName('en')];
            for (let n of needTranslates) {
                n.classList.replace("en", "kh")

            }
        }
    }
    $(document).ready(function() {
        // Setup AJAX CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // $('#employee_report').click(function(){
        //     console.log('hhhh')
        // })
        // Initializing DataTable
        $('#user-table').DataTable({
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            ajax: "{{url('admin/attendances')}}",

            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'user.name',
                    name: 'name'
                },
                // {
                //     "data":null,
                //     render: function(data,type,row){
                //         return `<div>  <button data-id="${row.id}" class="btn btn-sm btn-icon btn-info"  id="editBtn">${type.name}</button>   </div>`
                //     }
                // },
                {
                    data: 'checkin_time',
                    name: 'checkin_time'
                },
                {
                    data: 'checkout_time',
                    name: 'checkout_time'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                // {
                //     data: 'date',
                //     name: 'date'
                // },

            ],
        });

        $('#user-table').DataTable().on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });



        // Store new company or update company

    });
</script>
@endsection