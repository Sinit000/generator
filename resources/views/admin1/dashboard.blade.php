@extends('admin.layouts.master')
@section('title', 'Dashboard')

@section('css')
<!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/modules/fullcalendar/fullcalendar.min.css"> -->
<style>

    .en[data="dashboard"]::before {
        content: 'Dashboard'

    }

    .kh[data="dashboard"]::before {
        content: 'ផ្ទាំងការងារ'
    }

    .en[data="total_employee"]::before {
        content: 'Total Employee'

    }

    .kh[data="total_employee"]::before {
        content: 'ចំនួនបុគ្គលិកសរុប'
    }

    .en[data="on_time"]::before {
        content: 'On Time'

    }

    .kh[data="on_time"]::before {
        content: 'មកទាន់ពេល'
    }

    .en[data="late"]::before {
        content: 'Late'

    }

    .kh[data="late"]::before {
        content: 'យឺត'
    }

    .en[data="overtime"]::before {
        content: 'Overtime'

    }

    .kh[data="overtime"]::before {
        content: 'លើសម៉ោង'
    }

    .en[data="absent"]::before {
        content: 'Absent'

    }

    .kh[data="absent"]::before {
        content: 'ឈប់អត់ច្បាប់'
    }

    .en[data="leave"]::before {
        content: 'Leave'

    }

    .kh[data="leave"]::before {
        content: 'ច្បាប់'
    }

    .en[data="dayoff"]::before {
        content: 'Day Off'

    }

    .kh[data="dayoff"]::before {
        content: 'ឈប់សម្រាក'
    }

    .en[data="from_date"]::before {
        content: 'From Date'

    }

    .kh[data="leave_out"]::before {
        content: 'Leave out'
    }

    .en[data="leave_out"]::before {
        content: 'សុំចេញក្រៅ'

    }

    .kh[data="from_date"]::before {
        content: 'ចាប់ពីថ្ងៃ'
    }

    .en[data="to_date"]::before {
        content: 'To Date'

    }

    .kh[data="to_date"]::before {
        content: 'ដល់ថ្ងៃ'
    }

    .en[data="type"]::before {
        content: 'Type'

    }

    .kh[data="type"]::before {
        content: 'ប្រភេទ'
    }

    .en[data="duration"]::before {
        content: 'Duration'

    }

    .kh[data="duration"]::before {
        content: 'រយៈពេល'
    }

    .en[data="request_date"]::before {
        content: 'Request Date'

    }

    .kh[data="request_date"]::before {
        content: 'ថ្ងៃស្នើសុំ'
    }

    .en[data="action"]::before {
        content: 'Action'

    }

    .kh[data="action"]::before {
        content: 'សកម្មភាព'
    }

    .en[data="no"]::before {
        content: 'No'

    }

    .kh[data="no"]::before {
        content: 'លេខរៀង'
    }
    #bg{  
width: 200px;  
height: 200px;  
border: 4px solid blue;  
}
</style>
@endsection

@section('content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1 data="dashboard" id="title-family" class="en"></h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <i class="fa fa-home"></i>
                    <a href="{{ url('admin/dashboard') }}" data="dashboard" id="language-family" class="en">
                    </a>
                </div>
            </div>
        </div>

        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <!-- <div class="card">
                    <div class="card-header">
                        <h4>Welcome to Attendance App</h4>
                    </div>

                    </div> -->
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-primary">
                                    <i class="far fa-user"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4 data="total_employee" id="subtitle-family" class="en"></h4>
                                    </div>
                                    <div class="card-body">
                                        {{$data}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-success">
                                    <i class="far fa-file"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4 data="on_time" id="subtitle-family" class="en"></h4>
                                    </div>
                                    <div class="card-body">
                                        {{$checkin}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-warning">
                                    <i class="far fa-file"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4 data="late" id="subtitle-family" class="en"></h4>
                                    </div>
                                    <div class="card-body">
                                        {{$late}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-info">
                                    <i class="fas fa-circle"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4 data="overtime" id="subtitle-family" class="en"></h4>
                                    </div>
                                    <div class="card-body">
                                        {{$overtime}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
                            <div class="card card-statistic-1">
                                <div class="card-icon bg-danger">
                                    <i class="far fa-newspaper"></i>
                                </div>
                                <div class="card-wrap">
                                    <div class="card-header">
                                        <h4 data="absent" id="subtitle-family" class="en"></h4>
                                    </div>
                                    <div class="card-body">
                                        {{$absent}}
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
                                        <h4 data="leave" id="subtitle-family" class="en"></h4>
                                    </div>
                                    <div class="card-body">
                                        {{$leave}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- table -->

                    <!--  -->
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
    window.onload = function(){  
        
        langCode = localStorage.getItem("lang-code");
        if (!langCode) {
            langCode = "en";
            localStorage.setItem("lang-code", "en")
        }
        
        // if (langCode === "en") {
        //     console.log('onload');
        //     document.getElementById("subtitle-family").style.fontFamily = "Times New Roman"; 
        //     document.getElementById("language-family").style.fontFamily = "Times New Roman"; 
            
        // } else {
        //     // document.getElementById("subtitle-family").style.fontFamily = "Khmer OS"; 
        // }
    
    
    }  
    checkLanguage();

    function checkLanguage() {
        console.log(langCode);

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
            // document.getElementById("subtitle-family").style.fontFamily = "Times New Roman"; 
            // document.getElementById("language-family").style.fontFamily = "Times New Roman"; 

        } else {
           
            needTranslates = [...document.getElementsByClassName('en')];
            console.log('khmer translate')
            for (let n of needTranslates) {
                n.classList.replace("en", "kh")

            }
            // document.getElementById("subtitle-family").style.fontFamily = "Khmer OS"; 
        }

    }
    // if (langCode === "en") {
    //     console.log('en img')
    //     var imgEn = document.getElementById("img-en");
    //     imgEn.classList.remove('en-hidden')
    // } else {
    //     console.log('kh img')
    //     var imgEn = document.getElementById("img-kh");
    //     imgEn.classList.remove('kh-hidden')

    // }
    $(document).ready(function() {
        // Setup AJAX CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

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