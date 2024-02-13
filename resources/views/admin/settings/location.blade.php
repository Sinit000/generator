@extends('admin.layouts.master')
@section('title', 'Location')

@section('css')
<link rel="stylesheet" href="{{ asset('backend/modules/datatables/datatables.min.css') }}">
<style>
    .en[data="email"]::before {
        content: 'Email'

    }

    .kh[data="email"]::before {
        content: 'អុីម៉ែល'
    }

    .en[data="dashboard"]::before {
        content: 'Dashboard'

    }

    .kh[data="dashboard"]::before {
        content: 'ផ្ទាំងការងា'
    }

    .en[data="long"]::before {
        content: 'Longtitude'

    }

    .kh[data="long"]::before {
        content: 'រយៈបណ្តោយ'
    }

    .en[data="note"]::before {
        content: 'Role'

    }

    .kh[data="note"]::before {
        content: 'តួនាទី'
    }

    .en[data="qr"]::before {
        content: 'QR'

    }

    .kh[data="qr"]::before {
        content: 'ឃ្យូអរកូដ'
    }

    .en[data="user_name"]::before {
        content: 'User Name'

    }

    .kh[data="user_name"]::before {
        content: 'ឈ្មោះ'
    }

    .en[data="reason"]::before {
        content: 'Reason'

    }

    .kh[data="reason"]::before {
        content: 'មូលហេតុ'
    }

    .en[data="from_date"]::before {
        content: 'From Date'

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
        content: 'ល.រ'
    }

    .en[data="btn_close"]::before {
        content: 'Close'

    }

    .kh[data="btn_close"]::before {
        content: 'បិទ'
    }

    .en[data="btn_edit"]::before {
        content: 'Edit'

    }

    .kh[data="btn_edit"]::before {
        content: 'បញ្ជូន'
    }

    .en[data="start_date"]::before {
        content: 'Start Date'

    }

    .kh[data="start_date"]::before {
        content: 'ថ្ងៃចាប់ផ្តើម'
    }

    .en[data="end_date"]::before {
        content: 'End Date'

    }

    .kh[data="end_date"]::before {
        content: 'ថ្ងៃបញ្ជប់'
    }

    .en[data="base_salary"]::before {
        content: 'Base Salary'

    }

    .kh[data="base_salary"]::before {
        content: 'ប្រាក់ខែគោល'
    }

    .en[data="working"]::before {
        content: 'Working Schedule'

    }

    .kh[data="working"]::before {
        content: 'ចំនួនម៉ោងក្នុងមួយសប្តាហ៏'
    }

    .en[data="create_data"]::before {
        content: 'Create Data'

    }

    .kh[data="create_data"]::before {
        content: 'បង្កើតទិន្នន័យ'
    }

    .en[data="user"]::before {
        content: 'User'

    }

    .kh[data="user"]::before {
        content: 'អ្នកប្រើប្រាស់'
    }

    .en[data="create_data"]::before {
        content: 'Create Data'

    }

    .kh[data="create_data"]::before {
        content: 'បង្កើតទិន្នន័យ'
    }

    /*#language-family{*/
    /*    font-family:Khmer OS Battambang;*/
    /*}*/
</style>
@endsection

@section('content')
<!-- Modal -->
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
                        <label for="nip">Name <sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter name..." autocomplete="off">
                        <div class="invalid-feedback" id="valid-name"></div>
                    </div>

                    <div class="form-group">
                        <label for="no">email <sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Enter Latitude..." autocomplete="off">
                        <div class="invalid-feedback" id="valid-email"></div>
                    </div>
                    <div class="form-group">
                        <label for="off_day" id="p">Password <sup class="text-danger">*</sup></label>
                        <input type="password" class="form-control" id="password" name="password" autocomplete="off" placeholder="Enter Password...">
                                            <div class="invalid-feedback" id="valid-password"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Role</label>
                                <select class="form-control select2" id="role_id" name="role_id" style="width: 100%;">
                                    <option value="">Choose Role</option>
                                </select>
                                <div class="invalid-feedback" id="valid-role_id"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Company</label>
                                <select class="form-control select2" id="company_id" name="company_id" style="width: 100%;">
                                    <!-- <option value="">Choose Role</option> -->
                                </select>
                                
                            </div>
                        </div>
                    </div>
                    
                    

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



<!-- Main Content -->
<div class="main-content">

    <section class="section">
        <!-- <div id="app"> -->
        <!-- <ul>
                    <li v-for="(l,i) in list" :key="i">@{{l}}</li>
                </ul> -->

        <div class="section-header">
            <h1 data="user" id="title-family" class="en"></h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <i class="fa fa-home"></i>
                    <a href="{{ url('admin/dashboard') }}" data="dashboard" id="subtitle-family" class="en"></a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card card-info">
                @if(auth()->user()->role->name === "Owner" )
                <div class="card-header">
                    <button class="btn btn-info ml-auto" id="btn-add">
                        <i class="fas fa-plus-circle"></i>
                        Create User
                    </button>
                </div>
                @endif
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="user-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>
                                        <p class="en" data="no" id="subtitle-family"></p>
                                    </th>
                                    <th>
                                        <p class="en" data="user_name" id="subtitle-family"></p>
                                    </th>
                                    <th>
                                        <p class="en" data="email" id="subtitle-family"></p>
                                    </th>
                                    <th>
                                        <p class="en" data="note" id="subtitle-family"></p>
                                    </th>
                                    @if(auth()->user()->role->name === "Owner")
                                        <th>
                                            <p class="en" data="action" id="subtitle-family"></p>
                                        </th>
                                    @else
                                       <th></th>
                                    @endif

                                    
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- </div> -->

    </section>
</div>
@endsection

@section('js')
<script src="{{ asset('backend/modules/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('backend/modules/sweetalert/sweetalert.min.js') }}"></script>
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>
<!-- <script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script> -->
<!-- <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    
    <script>
        const { createApp } = Vue

        createApp({
            data() {
                return {
                    list: ['a', 'b', 'c']
                }
            },

            created(){
                console.log('vue')
            }
        }).mount('#app')
    </script> -->


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

        // Initializing DataTable
        $('#user-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{url('admin/user')}}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
               
                {
                    data: 'role.name',
                    name: 'role.name'
                },
                

                // {
                //     "data": null,
                //     render: function(data, type, row) {
                //         return `<div>  <button data-toggle="tooltip" data-original-title="Edit" data-id="${row.id}" class="btn btn-sm btn-icon btn-info"  id="editBtn"><i class="fa fa-edit"></i></button>   <button data-toggle="tooltip" data-original-title="Delete" data-id="${row.id}"  class="btn btn-sm btn-icon btn-danger" data-original-title="Delete"  id="deleteBtn"><i class="fa fa-trash-alt"></i></button></div>`
                //     }
                // },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center',
                    orderable: false,
                    searchable: false
                }
            ],
        });


        $('#user-table').DataTable().on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // Open Modal to Add new Category
        $('#btn-add').click(function() {
            $('#formModal').modal('show');
            $.ajax({
                type: "GET",
                url: "{{url('admin/location/componet')}}",
                // data:"",
                dataType: "json",
                success: function(response) {
                    // console.log(response.data);
                    console.log(response);
                    if (response.status == 404) {
                        toastr.error(response.message);

                    } else {

                        // console.log(response.work)
                        var selOpts = "";
                        for (i=0;i<response.role.length;i++)
                        {
                            
                            var id = response.role[i]['id'];
                            var val = response.role[i]['name'];
                            selOpts += "<option value='"+id+"'>"+val+"</option>";
                        }
                        $('#role_id').append(selOpts);

                        // timetable
                        var time = "";
                        
                        for (i = 0; i < response.user.length; i++) {
                            console.log(response.user[i]['name']);
                            var id = response.user[i]['id'];
                            var val = response.user[i]['name'];
                            // var val = response.location[i]['name'] +">"+ "from" + " "+ response.location[i]['on_duty_time']+" " +"to"+" "+ response.location[i]['off_duty_time'];
                            time += "<option value='" + id + "'>" + val + "</option>";
                        }
                        $('#company_id').append(time);
                    }
                }
            });
            $('.modal-title').html('Create Data');
            $('#company-form').trigger('reset');
            $('#btn-save').html('<i class="fas fa-check"></i> Save Data');
            $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
            $('#btn-save').val('save').removeAttr('disabled');
            $('.close').click(function() {
                // remove that from select value after save data to avoid dublicate data
                // console.log('close button');
                $('#role_id').find('option').remove().end().append('<option value="">Choose Role</option>').val('');
                $('#company_id').find('option').remove().end().append('<option value="">Chooose Company</option>').val('');
            })
            $('#btn-close').click(function() {
                // remove that from select value after save data to avoid dublicate data
                $('#role_id').find('option').remove().end().append('<option value="">Choose Role</option>').val('');
                $('#company_id').find('option').remove().end().append('<option value="">Chooose Company</option>').val('');
            })
        });

        // Store new company or update company
        $('#btn-save').click(function() {
            // save data state
            // send to backend
            var formData = {
                name: $('#name').val(),
                email: $('#email').val(),
                password: $('#password').val(),
                role_id: $('#role_id').val(),
                company_id: $('#company_id').val(),
            };

            var state = $('#btn-save').val();


            var type = "POST";
            var ajaxurl = "{{url('admin/user/store')}}";
            $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Saving...').attr("disabled", true);
            console.log(state);
            // update state
            if (state == "update") {
                console.log(state);
                $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Updating...').attr("disabled", true);
                var id = $('#id').val();
                type = "POST";
                ajaxurl = "{{ url('admin/user/update') }}" + '/' + id;
                console.log(ajaxurl);
            }

            $.ajax({
                type: type,
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function(data) {
                    console.log(state);
                    if (state == "save") {
                        if(data.code==0){
                            swal({
                            title: "Success!",
                            text: "Data has been added successfully!",
                            icon: "success",
                            timer: 3000
                        });
                        }else{
                            swal({
                                title: "Sorry!",
                                text: "An error occurred, please try again",
                                icon: "error",
                                timer: 3000
                            });
                        }
                       

                        $('#user-table').DataTable().draw(false);
                        $('#user-table').DataTable().on('draw', function() {
                            $('[data-toggle="tooltip"]').tooltip();
                        });
                    } else {
                        console.log(state);
                        swal({
                            title: "Success!",
                            text: "Data has been updated successfully!",
                            icon: "success",
                            timer: 3000
                        });

                        $('#user-table').DataTable().draw(false);
                        $('#user-table').DataTable().on('draw', function() {
                            $('[data-toggle="tooltip"]').tooltip();
                        });
                    }

                    $('#formModal').modal('hide');
                },
                error: function(data) {
                    try {
                        if (state == "save") {
                            if (data.responseJSON.errors.name) {
                                $('#name').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-name').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-name').html(data.responseJSON.errors.name);
                            }
                            
                            if (data.responseJSON.errors.email) {
                                $('#email').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-email').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-email').html(data.responseJSON.errors.email);
                            }
                            if (data.responseJSON.errors.password) {
                                $('#password').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-password').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-password').html(data.responseJSON.errors.password);
                            }
                            if (data.responseJSON.errors.role_id) {
                                $('#role_id').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-role_id').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-role_id').html(data.responseJSON.errors.role_id);
                            }


                            $('#btn-save').html('<i class="fas fa-check"></i> Save Changes');
                            $('#btn-save').removeAttr('disabled');
                        } else {
                            if (data.responseJSON.errors.name) {
                                $('#name').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-name').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-name').html(data.responseJSON.errors.name);
                            }
                            
                            if (data.responseJSON.errors.email) {
                                $('#email').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-email').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-email').html(data.responseJSON.errors.email);
                            }
                           
                            if (data.responseJSON.errors.role_id) {
                                $('#role_id').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-role_id').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-role_id').html(data.responseJSON.errors.role_id);
                            }
                            $('#btn-save').html('<i class="fas fa-check"></i> Update');
                            $('#btn-save').removeAttr('disabled');
                        }
                    } catch {
                        if (state == "save") {
                            swal({
                                title: "Sorry!",
                                text: "An error occurred, please try again",
                                icon: "error",
                                timer: 3000
                            });
                        } else {
                            swal({
                                title: "Sorry!",
                                text: "An error occurred, please try again",
                                icon: "error",
                                timer: 3000
                            });
                        }

                        $('#formModal').modal('hide');
                    }
                }
            });
        });
        //  Edit Category
        $(document).on('click', '#editBtn', function() {
            var id = $(this).attr('data-id');
            console.log(id);
            $.get("{{ url('admin/user/edit') }}" + '/' + id, function(data) {
                $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
                // show data on modal
                // $('#name').val(data.nip);
                $('#id').val(data.data.id);
                $('#name').val(data.data.name);
                
                $('#email').val(data.data.email);
                $('#password').val(data.data.password);
                const type = document.getElementById('password');
                // const p = document.getElementById('p');
                type.setAttribute('disabled', '');
                // type.setAttribute('p', '');
                console.log(data.data);
                // $('#company_id').val(data.company.name);
                var selOpts = "";
                console.log(data.company.name);
                selOpts=   $('select[name="company_id"]').append(`<option value="${data.company.id}" >${data.company.name}</option>`);
                                    // $('.editModal').append(selOpts);
                // $.each(data.user, function(key, value) {
                                       
                    // selOpts=   $('select[name="company_id"]').append(`<option value="${value.id}" >${value.name}</option>`)});
                                    // $('.editModal').append(selOpts);
                $('#formModal').find('#company_id').append(selOpts)
                //                       // timetable
                var time = "";
                // var x = document.getElementById('company_id');
                // x.setAttribute("type", "hidden");


                $.each(data.role, function(key, value) {
                    time = $('select[name="role_id"]')
                        .append(`<option value="${value.id}" ${value.id == data.data.role_id ? 'selected' : ''}>${value.name}</option>`)
                    

                });
                // $('#location_id').append(time);
                $('#formModal').find('#role_id').append(time);
               

                //                       // timetable

                // change value button save to update then state to save
                $('#btn-save').val('update').removeAttr('disabled');
                $('#formModal').modal('show');
                $('.modal-title').html('Edit Data');
                $('#null').html('<small id="null">Kosongkan jika tidak ingin di ubah</small>');
                $('#btn-save').html('<i class="fas fa-check"></i> Edit');
                $('.close').click(function() {
                    // remove that from select value after save data to avoid dublicate data
                    // console.log('close button');
                    $('#role_id').find('option').remove().end().append('<option value="">Choose Role</option>').val('');
                    $('#company_id').find('option').remove().end().append('<option value="">Chooose Company</option>').val('');
                })
                $('#btn-close').click(function() {
                    // remove that from select value after save data to avoid dublicate data
                    $('#role_id').find('option').remove().end().append('<option value="">Choose Role</option>').val('');
                    $('#company_id').find('option').remove().end().append('<option value="">Chooose Company</option>').val('');
                })
            }).fail(function() {
                swal({
                    title: "Sorry!",
                    text: "Faild to update data!",
                    icon: "error",
                    timer: 3000
                });
            });
        });



        // Delete company
        $(document).on('click', '#deleteBtn', function() {
            var id = $(this).attr('data-id');
            console.log(id);
            swal("Warning!", "Are you sure you want to delete?", "warning", {
                buttons: {
                    cancel: "NO!",
                    ok: {
                        text: "Yes!",
                        value: "ok"
                    }
                },
            }).then((value) => {
                switch (value) {
                    case "ok":
                        $.ajax({
                            type: "DELETE",
                            url: "{{ url('admin/location/delete') }}" + '/' + id,
                            success: function(data) {
                                if (data.code == 0) {
                                    $('#user-table').DataTable().draw(false);
                                    $('#user-table').DataTable().on('draw', function() {
                                        $('[data-toggle="tooltip"]').tooltip();
                                    });

                                    swal({
                                        title: "Success!",
                                        text: data.message,
                                        icon: "success",
                                        timer: 3000
                                    });
                                } else {
                                    swal({
                                        title: "Sorry!",
                                        text: data.message,
                                        icon: "error",
                                        timer: 3000
                                    });
                                }
                            },
                            error: function(data) {
                                swal({
                                    title: "Sorry!",
                                    text: "An error occurred, please try again",
                                    icon: "error",
                                    timer: 3000
                                });
                            }
                        });
                        break;

                    default:
                        swal({
                            title: "Oh Ya!",
                            text: "Data is not change",
                            icon: "info",
                            timer: 2
                        });
                        break;
                }
            });
        });

    });
    // get qr
</script>
@endsection