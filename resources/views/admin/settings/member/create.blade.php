@extends('admin.layouts.master')
@section('title', 'Create Employee')
@section('head','')

@section('css')
<style>
    .en[data="create_employee"]::before {
        content: 'Create Member'
    }

    .kh[data="create_employee"]::before {
        content: 'បង្កើតសមាជិក'
    }

    .en[data="create_data"]::before {
        content: 'Create Data'

    }

    .kh[data="create_data"]::before {
        content: 'បង្កើតទិន្នន័យ'
    }
    .en[data="img"]::before {
        content: 'Image'

    }

    .kh[data="img"]::before {
        content: 'រូបភាព'
    }
    .en[data="email"]::before {
        content: 'Email'

    }

    .kh[data="email"]::before {
        content: 'អ៊ីមែល'
    }
     .en[data="phone"]::before {
        content: 'Phone'

    }

    .kh[data="phone"]::before {
        content: 'លេខទូរសព្ទ័'
    }
     .en[data="position"]::before {
        content: 'Position'

    }

    .kh[data="position"]::before {
        content: "មុខតំណែងការងារ"
    }
    .en[data="school"]::before {
        content: 'School'

    }

    .kh[data="school"]::before {
        content: 'សាលា'
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
    .en[data="full_name"]::before {
        content: 'Name'

    }
    .kh[data="full_name"]::before {
        content: 'ឈ្មោះពេញ'
    }
    .en[data="gender"]::before {
        content: 'Gender'

    }
    .kh[data="gender"]::before {
        content: 'ភេទ'
    }
    .en[data="user_app"]::before {
        content: 'Username'

    }
    .kh[data="user_app"]::before {
        content: 'ឈ្មោះអ្នកប្រើប្រាស់'
    }
    .en[data="pass_app"]::before {
        content: 'Password'

    }
    .kh[data="pass_app"]::before {
        content: 'លេខសម្ងាត់'
    }
    .en[data="dob"]::before {
        content: 'Date of Birth'

    }
    .kh[data="dob"]::before {
        content: 'ថ្ងៃខែឆ្នាំកំណើត'
    }
    .en[data="nationality"]::before {
        content: 'Nationality'

    }
    .kh[data="nationality"]::before {
        content: 'សញ្ជាតិ'
    }
    .en[data="telegram"]::before {
        content: 'Telegram'

    }
    .kh[data="telegram"]::before {
        content: 'តេឡេក្រាម'
    }
    .en[data="facebook"]::before {
        content: 'Facebook'

    }
    .kh[data="facebook"]::before {
        content: 'ហ្វេកប៊ុក'
    }
     .en[data="address"]::before {
        content: 'Address'

    }
    .kh[data="address"]::before {
        content: 'អាស័យដ្ឋាន'
    }
     .en[data="role"]::before {
        content: 'Role'

    }
    .kh[data="role"]::before {
        content: 'តួនាទី'
    }
     .en[data="timetable"]::before {
        content: 'Choose Timetable'

    }
    .kh[data="timetable"]::before {
        content: 'ម៉ោងធ្វើការ'
    }
     .en[data="workday"]::before {
        content: 'Choose Workday'

    }
    .kh[data="workday"]::before {
        content: 'ថ្ងៃធ្វើការ'
    }
     .en[data="merital_status"]::before {
        content: 'Merital Status'

    }
    .kh[data="merital_status"]::before {
        content: 'ស្ខានភាព'
    }
    .en[data="year"]::before {
        content: 'Year finish with RTR'

    }
    .kh[data="year"]::before {
        content: 'ឆ្នាំបញ្ជប់'
    }
    .en[data="spouse_job"]::before {
        content: 'Job'

    }
    .kh[data="spouse_job"]::before {
        content: 'ការងារ'
    }
    .en[data="save_btn"]::before {
        content: 'Save Data'

    }
    .kh[data="save_btn"]::before {
        content: 'បញ្ជូន'
    }
    .en[data="back"]::before {
        content: 'Back'

    }
    .kh[data="back"]::before {
        content: 'ត្រឡប់ទៅវិញ'
    }
    .en[data="company"]::before {
        content: 'Company'

    }
    .kh[data="company"]::before {
        content: 'ក្រុមហ៊ុន'
    }
    /*#language-family{*/
    /*    font-family:Khmer OS Battambang;*/
    /*}*/
</style>
@endsection

@section('content')


<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1 data="create_employee" id="title-family" class="en"></h1>
            <!-- <h1>Employee Data</h1> -->
            <div class="section-header-breadcrumb">
               <div class="breadcrumb-item" >
                    <i class="fa fa-home" ></i>
                    <a href="{{ url('admin/dashboard') }}" data="dashboard" id="subtitle-family" class="en" ></a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card card-info">
                <div class="card-header">
                    <div class="ml-auto">
                        <a href="{{url('admin/member')}}" class="en btn btn-info " data="back" id="subtitle-family"></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card">
                        <form id="company-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="company_id" id="company_id" value="{{$company}}">
                            <!--<div class="card-header">-->
                            <!--    <h4 data="create_employee" id="subtitle-family" class="en" ></h4>-->
                            <!--</div>-->
                            <div class="card-body">
                                <!-- row one -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="staff_id"  data="no" id="subtitle-family" class="en"></label> 
                                            <input type="text"  class="form-control" id="no" name="no" placeholder="Enter member id..." autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name" data="full_name" id="subtitle-family" class="en" ><sup class="text-danger">*</sup></label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter  name..." autocomplete="off">
                                            <div class="invalid-feedback" id="valid-name"></div>
                                        </div>
                                    </div>
                                    <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gender" data="gender" id="subtitle-family" class="en" > <sup class="text-danger">*</sup></label>
                                            <select class="form-control select2" id="gender" name="gender" style="width: 100%;">
                                                <option value="">Choose gender</option>
                                                <option value="M">Male</option>
                                                <option value="F">Female</option>
                                            </select>
                                            <div class="invalid-feedback" id="valid-gender"></div>
                                        </div>
                                    </div> -->
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name"  data="dob" id="subtitle-family" class="en"> </label>
                                            <div class="bootstrap-timepicker ">
                                                <input type="date" class="form-control datepicker" id="dob" name="dob">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- row two -->
                                <div class="row">
                                    <!-- <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="staff_id" data="nationality" id="subtitle-family" class="en" ></label>
                                            <select class="form-control  select2" id="nationality" name="nationality" style="width: 100%;">
                                                <option value="">Choose Nationaily</option>
                                                <option value="Cambodian">Cambodian</option>
                                                <option value="Thai">Thai</option>
                                                <option value="Laos">Loas</option>
                                                <option value="VietNam">VietName</option>
                                                <option value="Chinese">Chinese</option>
                                                <option value="Korean">Korean</option>
                                                <option value="Japanese">Japanese</option>
                                            </select>
                                            <input type="text" class="form-control" id="nationality" name="nationality"
                                                    placeholder="Enter nationality..." autocomplete="off">
                                        </div>
                                    </div> -->
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for=""  data="phone" id="subtitle-family" class="en"></label>
                                            <input type="text" class="form-control" id="phone_number" name="phone_number" placeholder="Enter employee phone..." autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for=""  data="email" id="subtitle-family" class="en"></label>
                                            <input type="email" class="form-control" id="email" name="email" autocomplete="off" placeholder="Enter email.....">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for=""  data="facebook" id="subtitle-family" class="en"> </label>
                                            <input type="text" class="form-control" id="facebook" name="facebook" placeholder="Enter facebook..." autocomplete="off">
                                        </div>
                                    </div> 
                                </div>
                                <!-- row three -->
                                <div class="row">
                                    
                                    
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for=""  data="telegram" id="subtitle-family" class="en"> </label>
                                            <input type="text" class="form-control" id="telegram" name="telegram" placeholder="Enter telegram..." autocomplete="off">
                                        </div>
                                    </div> 
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for=""  data="address" id="subtitle-family" class="en"></label>
                                            
                                            <textarea class="form-control" id="address" name="address"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gender"  data="position" id="subtitle-family" class="en"> <sup class="text-danger">*</sup></label>
                                            <select class="form-control select2" id="role_id" name="role_id" style="width: 100%;">
                                                <option value="">Choose Role</option>
                                                @foreach($role as $r)
                                                <option value="{{$r->id}}">{{$r->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="valid-role_id"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- row 4 -->
                               
                                <!-- row 5 -->
                                <div class="row">
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name"  data="school" id="subtitle-family" class="en"> <sup class="text-danger">*</sup></label>
                                            <select class="form-control select2" id="school_id" name="school_id" style="width: 100%;">
                                                <option value="">Choose School</option>
                                                @foreach($school as $r)
                                                <option value="{{$r->id}}">{{$r->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="valid-department"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for=""  data="year" id="subtitle-family" class="en"></label>
                                            <select class="form-control select2" id="start_year" name="start_year" style="width: 100%;">
                                                <option value=""  >Choose Start Year</option>
                                                
                                                <option value="2008">2008</option>
                                                <option value="2009">2009</option>
                                                <option value="2010">2010</option>
                                                <option value="2011">2011</option>
                                                <option value="2012">2012</option>
                                                <option value="2013">2013</option>
                                                <option value="2014">2014</option>
                                                <option value="2015">2015</option>
                                                <option value="2016">2016</option>
                                                <option value="2017">2017</option>
                                                <option value="2018">2018</option>
                                                <option value="2019">2019</option>
                                                <option value="2020">2020</option>
                                                <option value="2021">2021</option>
                                                <option value="2022">2022</option>
                                                <option value="2023">2023</option>
                                                <option value="2024">2024</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for=""  data="year" id="subtitle-family" class="en"></label>
                                            <select class="form-control select2" id="end_year" name="end_year" style="width: 100%;">
                                                <option value=""  >Choose End Year</option>
                                                
                                                <option value="2008">2008</option>
                                                <option value="2009">2009</option>
                                                <option value="2010">2010</option>
                                                <option value="2011">2011</option>
                                                <option value="2012">2012</option>
                                                <option value="2013">2013</option>
                                                <option value="2014">2014</option>
                                                <option value="2015">2015</option>
                                                <option value="2016">2016</option>
                                                <option value="2017">2017</option>
                                                <option value="2018">2018</option>
                                                <option value="2019">2019</option>
                                                <option value="2020">2020</option>
                                                <option value="2021">2021</option>
                                                <option value="2022">2022</option>
                                                <option value="2023">2023</option>
                                                <option value="2024">2024</option>
                                            </select>
                                        </div>
                                    </div>
                                    

                                </div>
                                <!--  -->
                                <div class="row">
                                    
                                    
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="staff_id"  data="merital_status" id="subtitle-family" class="en"> </label>
                                            <select class="form-control select2" id="merital_status" name="merital_status" style="width: 100%;">
                                                <option value=""  >Choose Status</option>
                                                
                                                <option value="single">Single</option>
                                                <option value="married">Married</option>
                                                <option value="divorced">Divorce</option>
                                            </select>
                                        </div>
                                    </div> 
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for=""  data="parent_id" id="subtitle-family" class="en"> </label>
                                            <select class="form-control select2" id="parent_id" name="parent_id" style="width: 100%;">
                                                <option value="">Choose SM</option>
                                                @foreach($sm as $r)
                                                <option value="{{$r->id}}">{{$r->name}}</option>
                                                @endforeach
                                                
                                                
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!-- row 6 -->
                                <div class="row">
                                    
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for=""  data="spouse_job" id="subtitle-family" class="en"> </label>
                                            <select class="form-control select2" id="job_id" name="job_id" style="width: 100%;">
                                                <option value="">Choose job</option>
                                                
                                                @foreach($job as $r)
                                                <option value="{{$r->id}}">{{$r->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for=""  data="company" id="subtitle-family" class="en"> </label>
                                            <input type="text" class="form-control" id="company" name="company" placeholder="Enter company..." autocomplete="off">
                                        </div>
                                    </div> 
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gender"  data="img" id="subtitle-family" class="en"></label>
                                            <div class="col-md-4">
                                                <input type="file" name="profile_url" id="profile_url" multiple><br><br>
                                                <img id="img-show" src="" style="width:300px;"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                               
                                <!-- row 7 -->
                                
                                <!-- <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name"  data="device_imei" id="subtitle-family" class="en">Device ID(Mobile ) </label>
                                            <input type="text" class="form-control" id="device_imei" name="device_imei" placeholder="Enter  device_imei..." autocomplete="off">

                                        </div>
                                    </div>
                                    
                                </div> -->

                            </div>
                            <div class="card-footer text-right">
                                <button type="button" id="btn-save"  class="btn btn-info"   >

                                    <i class="fas fa-check"></i>
                                    Save Changes
                                    
                                </button>
                            </div>
                        </form>
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
checkLanguage()
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
        $('#profile_url').change(function(){
            var file = this.files[0];
            console.log(file);
            if (file){
                let reader = new FileReader();
                reader.onload = function(event){
                    console.log(event.target.result);
                    $('#img-show').attr('src', event.target.result);
                }
                reader.readAsDataURL(file);
            }
        });

        // Initializing DataTable


        // Open Modal to Add new Category
        // $('#btn-add').click(function() {
        //     $('#formModal').modal('show');
        //     $('.modal-title').html('Create Data');
        //     $('#company-form').trigger('reset');
        //     $('#btn-save').html('<i class="fas fa-check"></i> Save Data');
        //     $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
        //     $('#btn-save').val('save').removeAttr('disabled');
        // });
        // $('#btn-save').html('<i class="fas fa-check"></i> Save Data');
        // $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
        // $('#btn-save').val('save').removeAttr('disabled');

        // Store new company or update company
        $('#btn-save').click(function() {
            
            // address.addEventListener('input', function handleChange(event) {
            //     // console.log(event.target.value);
            //     console.log('hh')
            // });
            var fd = new FormData();
            var x = document.getElementById("address").value;
            var img = $('#profile_url').val();
            
            fd.append('no', $('#no').val());
            fd.append('name', $('#name').val());
            fd.append('parent_id', $('#parent_id').val());
            fd.append('start_year', $('#start_year').val());
            fd.append('end_year', $('#end_year').val());
            fd.append('dob', $('#dob').val());
            // fd.append('nationality', $('#nationality').val());
            fd.append('email', $('#email').val());
            fd.append('job_id', $('#job_id').val());
            fd.append('merital_status', $('#merital_status').val());
            fd.append('facebook', $('#facebook').val());
            fd.append('telegram', $('#telegram').val());
            fd.append('phone_number', $('#phone_number').val());
           
           
            fd.append('school_id', $('#school_id').val());
            fd.append('company', $('#company').val());
            // fd.append('role_id',$('#role_id').val());
            fd.append('year',$('#year').val());
           
            fd.append('address',x);
            if (img) {
                fd.append('profile_url', $('#profile_url')[0].files[0]);
            } else {
                console.log('no image');
            }
            var state = $('#btn-save').val();
            var type = "POST";
            var ajaxurl = "{{url('admin/member/store')}}";
            $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Saving...').attr("disabled", true);
            
           
          


            $.ajax({
                mimeType: "multipart/form-data",
                type: type,
                url: ajaxurl,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: fd,
                dataType: 'json',
                cache: false,
                processData: false,
                contentType: false,
                success: function(data) {

                    // console.log("Sinit")
                    // console.log(data);
                    if(data.code==0){
                        swal({
                        title: "Success!",
                        text: data.message,
                        icon: "success",
                        timer: 3000
                        });
                        $("#company-form")[0].reset();
                        $('#img-show').attr('src', "");
                    }else{
                        swal({
                        title: "Success!",
                        text: data.message,
                        icon: "error",
                            timer: 3000
                        });
                    }
                   
                    
                    $('#btn-save').html('<i class="fas fa-check"></i> Save Changes');
                    $('#btn-save').removeAttr('disabled');
                    

                    // $('#formModal').modal('hide');
                },
                error: function(data) {
                    console.log(data);
                    try {
                        if (data.responseJSON.errors.name) {
                            $('#name').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-name').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-name').html(data.responseJSON.errors.name);
                        }
                        if (data.responseJSON.errors.gender) {
                            $('#gender').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-gender').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-gender').html(data.responseJSON.errors.gender);
                        }

                        // if (data.responseJSON.errors.username) {
                        //     $('#username').removeClass('is-valid').addClass('is-invalid');
                        //     $('#valid-username').removeClass('valid-feedback').addClass('invalid-feedback');
                        //     $('#valid-username').html(data.responseJSON.errors.username);
                        // }
                        // if (data.responseJSON.errors.password) {
                        //     $('#password').removeClass('is-valid').addClass('is-invalid');
                        //     $('#valid-password').removeClass('valid-feedback').addClass('invalid-feedback');
                        //     $('#valid-password').html(data.responseJSON.errors.password);
                        // }
                        if (data.responseJSON.errors.role_id) {
                            $('#role_id').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-role_id').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-role_id').html(data.responseJSON.errors.role_id);
                        }
                        // if (data.responseJSON.errors.role_id) {
                        //     $('#role_id').removeClass('is-valid').addClass('is-invalid');
                        //     $('#valid-role').removeClass('valid-feedback').addClass('invalid-feedback');
                        //     $('#valid-role').html(data.responseJSON.errors.role_id);
                        // }
                        if (data.responseJSON.errors.school_id) {
                            $('#school_id').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-school_id').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-school_id').html(data.responseJSON.errors.school_id);
                        }
                        // if (data.responseJSON.errors.role_id) {
                        //     $('#role_id').removeClass('is-valid').addClass('is-invalid');
                        //     $('#valid-role_id').removeClass('valid-feedback').addClass('invalid-feedback');
                        //     $('#valid-role_id').html(data.responseJSON.errors.role_id);
                        // }
                        // if (data.responseJSON.errors.timetable_id) {
                        //     $('#timetable_id').removeClass('is-valid').addClass('is-invalid');
                        //     $('#valid-timetable_id').removeClass('valid-feedback').addClass('invalid-feedback');
                        //     $('#valid-timetable_id').html(data.responseJSON.errors.timetable_id);
                        // }
                        // if (data.responseJSON.errors.workday_id) {
                        //     $('#workday_id').removeClass('is-valid').addClass('is-invalid');
                        //     $('#valid-workday_id').removeClass('valid-feedback').addClass('invalid-feedback');
                        //     $('#valid-workday_id').html(data.responseJSON.errors.workday_id);
                        // }
                        $('#btn-save').html('<i class="fas fa-check"></i> Save Changes');
                        $('#btn-save').removeAttr('disabled');
                    } catch {
                        // console.log(data);
                        swal({
                            title: "Sorry!",
                            text: "An error occurred, please try again",
                            icon: "error",
                            timer: 3000
                        });
                        $('#btn-save').html('<i class="fas fa-check"></i> Save Changes');
                        $('#btn-save').removeAttr('disabled');

                        // $('#formModal').modal('hide');
                    }
                }
            });
        });
        //  Edit Category




    });
</script>

@endsection