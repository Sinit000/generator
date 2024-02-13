@extends('admin.layouts.master')
@section('title', 'Create Employee')
@section('head','Create Employee')

@section('css')

@endsection

@section('content')


<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <!-- <h1>Employee Data</h1> -->
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <!-- <a href="">
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
                        <a href="{{url('admin/user')}}" class="btn btn-info ">Back</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="card">
                        <form id="company-form" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="id" id="id">
                            <div class="card-header">
                                <h4>Create Employees</h4>
                            </div>
                            <div class="card-body">
                                <!-- row one -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="staff_id">Staff ID </label>
                                            <input type="text" class="form-control" id="no" name="no" placeholder="Enter staff id..." autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Full name <sup class="text-danger">*</sup></label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter  name..." autocomplete="off">
                                            <div class="invalid-feedback" id="valid-name"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gender">Gender <sup class="text-danger">*</sup></label>
                                            <select class="form-control select2" id="gender" name="gender" style="width: 100%;">
                                                <option value="">Choose gender</option>
                                                <option value="M">Male</option>
                                                <option value="F">Female</option>
                                            </select>
                                            <div class="invalid-feedback" id="valid-gender"></div>
                                        </div>
                                    </div>
                                </div>
                                <!-- row two -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="staff_id">Nationality</label>
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
                                            <!-- <input type="text" class="form-control" id="nationality" name="nationality"
                                                    placeholder="Enter nationality..." autocomplete="off"> -->
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Date of Birth </label>
                                            <div class="bootstrap-timepicker ">
                                                <input type="date" class="form-control datepicker" id="dob" name="dob">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="form-group">
                                                <label for="staff_id">Card Number</label>
                                                <input type="text" class="form-control" id="card_number" name="card_number" placeholder="Enter card number..." autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- row three -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Office Tel</label>
                                            <input type="text" class="form-control" id="office_tel" name="office_tel" placeholder="Enter office tel..." autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Employee phone</label>
                                            <input type="text" class="form-control" id="employee_phone" name="employee_phone" placeholder="Enter employee phone..." autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Email Address</label>
                                            <input type="email" class="form-control" id="email" name="email" autocomplete="off" placeholder="Enter email.....">
                                        </div>
                                    </div>
                                </div>
                                <!-- row 4 -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Username <sup class="text-danger">*</sup></label>
                                            <input type="text" class="form-control" id="username" name="username" placeholder="Enter  username..." autocomplete="off">
                                            <div class="invalid-feedback" id="valid-username"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Password <sup class="text-danger">*</sup></label>
                                            <input type="password" class="form-control" id="password" name="password" autocomplete="off" placeholder="Enter Password...">
                                            <div class="invalid-feedback" id="valid-password"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Address</label>

                                            <textarea class="form-control" id="address" name="address"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- row 5 -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gender">Position <sup class="text-danger">*</sup></label>
                                            <select class="form-control select2" id="position_id" name="position_id" style="width: 100%;">
                                                <option value="">Choose position</option>
                                                @foreach($position as $r)
                                                <option value="{{$r->id}}">{{$r->position_name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="valid-position"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Department <sup class="text-danger">*</sup></label>
                                            <select class="form-control select2" id="department_id" name="department_id" style="width: 100%;">
                                                <option value="">Choose Department</option>
                                                @foreach($department as $r)
                                                <option value="{{$r->id}}">{{$r->department_name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="valid-department"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Role <sup class="text-danger">*</sup></label>
                                            <select class="form-control select2" id="role_id" name="role_id" style="width: 100%;">
                                                <option value="">Choose role</option>
                                                @foreach($role as $r)
                                                <option value="{{$r->id}}">{{$r->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="valid-role_id"></div>
                                        </div>
                                    </div>

                                </div>
                                <!--  -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Timetable <sup class="text-danger">*</sup></label>
                                            <select class="form-control select2" id="timetable_id" name="timetable_id" style="width: 100%;">
                                                <option value="">Choose timetable</option>
                                                @foreach($timetable as $r)
                                                <option value="{{$r->id}}">from {{$r->on_duty_time}} to {{$r->off_duty_time}} </option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="valid-timetable_id"></div>

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Workday <sup class="text-danger">*</sup></label>
                                            <select class="form-control select2" id="workday_id" name="workday_id" style="width: 100%;">
                                                <option value="">Choose workday</option>
                                                @foreach($workday as $r)
                                                <option value="{{$r->id}}">Workday {{$r->working_day}} and Dayoff {{$r->off_day}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback" id="valid-workday_id"></div>

                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="staff_id">Marital Status </label>
                                            <select class="form-control select2" id="merital_status" name="merital_status" style="width: 100%;">
                                                <option value="">Choose status</option>
                                                <option value="single">Single</option>
                                                <option value="married">Married</option>
                                                <option value="divorced" >Divorce</option>
                                            </select>
                                        </div>

                                    </div>

                                </div>
                                <!-- row 6 -->
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="name">Minor Children </label>
                                            <input type="text" class="form-control" id="minor_children" name="minor_children" placeholder="Enter  number of children..." autocomplete="off">

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="">Spouse job </label>
                                            <select class="form-control select2" id="spouse_job" name="spouse_job" style="width: 100%;">
                                                <option value="">Choose job</option>
                                                <option value="housewife">Housewife</option>
                                                <option value="not housewife">Not Housewife</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gender">Profile</label>
                                            <div class="col-md-4">
                                                <input type="file" name="profile_url" id="profile_url"  multiple><br><br>
                                                <img id="img-show" src="" style="width:300px;"/>
                                                <!-- <input type="file" id="img" hidden />
                                                <button id="select-img" >Select</button>
                                                <img id="img-show" src="" style="width:300px;" /> -->
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="card-footer text-right">
                                <button type="button" id="btn-save" class="btn btn-info">
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
    
            
    $(document).ready(function() {
        // Setup AJAX CSRF
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // showImage()

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
          $('#profile_url').change(function(){
            const file = this.files[0];
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
        //   $('#profile_url').click(function(){
        //     var img = $('#profile_url').val(); 
            

        //     if(img){
        //         var image = img.target.files[0];
        //         console.log(image);
        //         console.log(image);
        //         // const btnSelect = document.getElementById('select-img');
        //         const imgFile = document.getElementById("profile_url")
        //         const imgShow = document.getElementById("img-show")
        //         // btnSelect.addEventListener("click",()=>imgFile.click())
        //         imgFile.addEventListener("change",(e)=>{
        //             const f = e.target.files[0]
        //             if(f){
        //                 const url = URL.createObjectURL(f);
        //                 imgShow.src=url;
        //             }
        //         })
        //     }else{
        //         console.log('ttt')
                
        //     }
            
        //   })
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
            fd.append('gender', $('#gender').val());
            fd.append('dob', $('#dob').val());
            fd.append('nationality', $('#nationality').val());
            fd.append('email', $('#email').val());
            fd.append('spouse_job', $('#spouse_job').val());
            fd.append('merital_status', $('#merital_status').val());
            fd.append('minor_children', $('#minor_children').val());
            fd.append('employee_phone', $('#employee_phone').val());
            fd.append('card_number', $('#card_number').val());
            fd.append('office_tel', $('#office_tel').val());
            fd.append('username', $('#username').val());
            fd.append('password', $('#password').val());
            fd.append('position_id', $('#position_id').val());
            fd.append('department_id', $('#department_id').val());
            fd.append('role_id', $('#role_id').val());
            fd.append('timetable_id', $('#timetable_id').val());
            fd.append('workday_id', $('#workday_id').val());
            fd.append('address', x);
            if (img) {
                fd.append('profile_url', $('#profile_url')[0].files[0]);
            } else {
                console.log('no image');
            }
            var state = $('#btn-save').val();
            var type = "POST";
            var ajaxurl = "{{url('admin/user/store')}}";
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
                    if (data.code == 0) {
                        swal({
                            title: "Success!",
                            text: data.message,
                            icon: "success",
                            timer: 3000
                        });
                        $('#btn-save').html('<i class="fas fa-check"></i> Save Changes');
                        $('#btn-save').removeAttr('disabled');
                        $("#company-form")[0].reset();
                        $('#img-show').attr('src', "");
                    }else{
                        swal({
                            title: "Sorry!",
                            text: data.message,
                            icon: "error",
                            timer: 3000
                        });
                        $('#btn-save').html('<i class="fas fa-check"></i> Save Changes');
                        $('#btn-save').removeAttr('disabled');
                    }


                   

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

                        if (data.responseJSON.errors.username) {
                            $('#username').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-username').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-username').html(data.responseJSON.errors.username);
                        }
                        if (data.responseJSON.errors.password) {
                            $('#password').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-password').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-password').html(data.responseJSON.errors.password);
                        }
                        if (data.responseJSON.errors.position_id) {
                            $('#position_id').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-position').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-position').html(data.responseJSON.errors.position_id);
                        }
                        // if (data.responseJSON.errors.role_id) {
                        //     $('#role_id').removeClass('is-valid').addClass('is-invalid');
                        //     $('#valid-role').removeClass('valid-feedback').addClass('invalid-feedback');
                        //     $('#valid-role').html(data.responseJSON.errors.role_id);
                        // }
                        if (data.responseJSON.errors.department_id) {
                            $('#department_id').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-department').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-department').html(data.responseJSON.errors.department_id);
                        }
                        if (data.responseJSON.errors.role_id) {
                            $('#role_id').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-role_id').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-role_id').html(data.responseJSON.errors.role_id);
                        }
                        if (data.responseJSON.errors.timetable_id) {
                            $('#timetable_id').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-timetable_id').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-timetable_id').html(data.responseJSON.errors.timetable_id);
                        }
                        if (data.responseJSON.errors.workday_id) {
                            $('#workday_id').removeClass('is-valid').addClass('is-invalid');
                            $('#valid-workday_id').removeClass('valid-feedback').addClass('invalid-feedback');
                            $('#valid-workday_id').html(data.responseJSON.errors.workday_id);
                        }
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