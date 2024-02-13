@extends('admin.layouts.master')
@section('title', 'Workday ')

@section('css')
<link rel="stylesheet" href="{{ asset('backend/modules/datatables/datatables.min.css') }}">
<style>
    
    .en[data="usage"]::before {
        content: 'Please Noted that all the numbers below are define day of the week!'

    }

    .kh[data="usage"]::before {
        content: 'សូមបញ្ជាក់ថាលេខខាងក្រោមនេះតំណាងអោយថ្ងៃនៃសប្តាហ៏'
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
        content: 'No'

    }

    .kh[data="note"]::before {
        content: 'ចំណាំ'
    }
     .en[data="qr"]::before {
        content: 'QR'

    }

    .kh[data="qr"]::before {
        content: 'ឃ្យូអរកូដ'
    }
    
    .en[data="user_name"]::before {
        content: 'Employee Name'

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
    .en[data="location"]::before {
        content: 'Location'

    }
    .kh[data="location"]::before {
        content: 'ទីតាំង'
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
                        <label for="working_day">Working Day <sup class="text-danger">*</sup></label>
                        <div class="div">
                            <label for="">Please enter only number with comma e.g 1,2,3,4,5,6</label>
                        </div>
                        <input type="text" class="form-control" id="working_day" name="working_day" placeholder="Enter workday..." autocomplete="off">
                        <div class="invalid-feedback" id="valid-working_day"></div>
                    </div>
                    <div class="form-group">
                        <label for="off_day">Off Day <sup class="text-danger">*</sup></label>
                        <div class="div">
                            <label for="">Please enter only number with comma e.g 0,6</label>
                        </div>
                        <input type="text" class="form-control" id="off_day" name="off_day" placeholder="Enter off day..." autocomplete="off">
                        <div class="invalid-feedback" id="valid-off_day"></div>
                    </div>
                    <div class="form-group">
                        <label for="name">Notes </label>
                        <input type="text" class="form-control" id="notes" name="notes" placeholder="Enter notes..." autocomplete="off">
                        <!-- <div class="invalid-feedback" id="valid-name"></div> -->
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
        <div class="section-header">
           <h1 data="workday" id="title-family" class="en"></h1>
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
                            <button class="en btn btn-info ml-auto" id="btn-add" data="create_data" id="subtitle-family">
                                <!--<i class="fas fa-plus-circle"></i>-->
                                
                            </button>
                        </div>
                <div class=" container">
                    <h5 class="en" data="usage" id="subtitle-family"></h5>
                    <div class="row">
                        <ol>
                            <li>Monday</li>
                            <li>Tuesday</li>
                            <li>Wednesday</li>

                        </ol>
                        <ul style="list-style-type:none;">
                            <li>4. Thursday</li>
                            <li>5 .Friday</li>

                        </ul>
                        <ul style="list-style-type:none;">
                            <li>6. Saturday</li>
                            <li>0. Sunday</li>

                        </ul>
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="user-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Working day</th>
                                    <th>Off day</th>
                                    <th>Notes</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                        </table>
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

        // Initializing DataTable
        $('#user-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{url('admin/workday')}}",
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
                    data: 'working_day',
                    name: 'working_day'
                },
                {
                    data: 'off_day',
                    name: 'off_day'
                },
                {
                    data: 'notes',
                    name: 'notes'
                },
                {
                    "data": null,
                    render: function(data, type, row) {
                        return `<div>  <button data-toggle="tooltip" data-original-title="Edit" data-id="${row.id}" class="btn btn-sm btn-icon btn-info"  id="editBtn"><i class="fa fa-edit"></i></button>   <button data-toggle="tooltip" data-original-title="Delete" data-id="${row.id}"  class="btn btn-sm btn-icon btn-danger" data-original-title="Delete"  id="deleteBtn"><i class="fa fa-trash-alt"></i></button></div>`
                    }
                },
                // {
                //     data: 'action',
                //     name: 'action',
                //     className: 'text-center',
                //     orderable: false,
                //     searchable: false
                // }
            ],
        });

        $('#user-table').DataTable().on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        // Open Modal to Add new Category
        $('#btn-add').click(function() {
            $('#formModal').modal('show');
            $('.modal-title').html('Create Data');
            $('#company-form').trigger('reset');
            $('#btn-save').html('<i class="fas fa-check"></i> Save Data');
            $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
            $('#btn-save').val('save').removeAttr('disabled');
        });

        // Store new company or update company
        $('#btn-save').click(function() {
            // save data state
            var formData = {
                name: $('#name').val(),
                working_day: $('#working_day').val(),
                off_day: $('#off_day').val(),
                notes: $('#notes').val(),
            };

            var state = $('#btn-save').val();

            var type = "POST";
            var ajaxurl = "{{url('admin/workday/store')}}";
            $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Saving...').attr("disabled", true);
            console.log(state);
            // update state
            if (state == "update") {
                console.log(state);
                $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Updating...').attr("disabled", true);
                var id = $('#id').val();
                type = "PUT";
                ajaxurl = "{{ url('admin/workday/update') }}" + '/' + id;
                console.log(ajaxurl);
            }

            $.ajax({
                type: type,
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    console.log(data.message);
                    if (state == "save") {
                        if(data.code ==0){
                            swal({
                                title: "Success!",
                                text: data.message,
                                icon: "success",
                                timer: 3000
                            });
                        }else{
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
                    } else {
                        if(data.code == 0){
                            swal({
                                title: "Success!",
                                text: "Data has been updated successfully!",
                                icon: "success",
                                timer: 3000
                            });
                        }else{
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
                            if (data.responseJSON.errors.working_day) {
                                $('#working_day').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-working_day').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-working_day').html(data.responseJSON.errors.working_day);
                            }
                            if (data.responseJSON.errors.off_day) {
                                $('#off_day').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-off_day').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-off_day').html(data.responseJSON.errors.off_day);
                            }
                            if (data.responseJSON.errors.password) {
                                $('#password').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-password').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-password').html(data.responseJSON.errors.password);
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
                            if (data.responseJSON.errors.kabupaten) {
                                $('#kabupaten').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-kabupaten').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-kabupaten').html(data.responseJSON.errors.kabupaten);
                            }
                            if (data.responseJSON.errors.password) {
                                $('#password').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-password').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-password').html(data.responseJSON.errors.password);
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
            $.get("{{ url('admin/workday/edit') }}" + '/' + id, function(data) {
                $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
                // show data on modal
                // $('#name').val(data.nip);
                $('#id').val(data.id);
                $('#name').val(data.name);
                $('#working_day').val(data.working_day);
                $('#off_day').val(data.off_day);
                $('#notes').val(data.notes);
                // change value button save to update then state to save
                $('#btn-save').val('update').removeAttr('disabled');
                $('#formModal').modal('show');
                $('.modal-title').html('Edit Data');
                $('#null').html('<small id="null">Kosongkan jika tidak ingin di ubah</small>');
                $('#btn-save').html('<i class="fas fa-check"></i> Edit');
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
                            url: "{{ url('admin/workday/delete') }}" + '/' + id,
                            success: function(data) {
                                if (data.code == 0) {
                                    $('#user-table').DataTable().draw(false);
                                    $('#user-table').DataTable().on('draw', function() {
                                        $('[data-toggle="tooltip"]').tooltip();
                                    });

                                    swal({
                                        title: "Success!",
                                        text: "Data has been deleted successfully!",
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
</script>
@endsection