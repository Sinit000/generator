@extends('admin.layouts.master')
@section('title', 'Approve')

@section('css')
    <link rel="stylesheet" href="{{ asset('backend/modules/datatables/datatables.min.css') }}">
    <style>
    .en[data="language"]::before {
        content: 'English'
    }

    .kh[data="language"]::before {
        content: 'ភាសា'
    }

    .en[data="approve_leave"]::before {
        content: 'Approve Leave'

    }

    .kh[data="approve_leave"]::before {
        content: 'អនុម័តច្បាប់'
    }
    .en[data="dashboard"]::before {
        content: 'Dashboard'

    }

    .kh[data="dashboard"]::before {
        content: 'ផ្ទាំងការងា'
    }
    .en[data="approve_list"]::before {
        content: 'Approve List'

    }

    .kh[data="approve_list"]::before {
        content: 'បញ្ជីអនុម័ត'
    }
     .en[data="edit_data"]::before {
        content: 'Edit Data'

    }

    .kh[data="edit_data"]::before {
        content: 'កែប្រែទិន្នន័យ'
    }
     .en[data="status"]::before {
        content: 'Status'

    }

    .kh[data="status"]::before {
        content: 'ស្ថានភាព'
    }
    .en[data="leave_deduction"]::before {
        content: 'Leave Deduction'

    }

    .kh[data="leave_deduction"]::before {
        content: 'កាត់លុយ'
    }
    .en[data="attachment"]::before {
        content: 'Attachment'

    }
    .kh[data="attachment"]::before {
        content: 'រូបភា់ព'
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
    .en[data="leave_type"]::before {
        content: 'Leavetype'

    }
    .kh[data="leave_type"]::before {
        content: 'ប្រភេទច្បាប់'
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
    .en[data="leave_dedution"]::before {
        content: 'Deduction'

    }
    .kh[data="leave_dedution"]::before {
        content: 'កាត់លុយ'
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
                <h5 class="en modal-title" data="edit_data" id="subtitle-family">
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
                        <label for="status" class="en" data="status" id="subtitle-family"> <sup class="text-danger">*</sup></label>
                        <select class="form-control select2"  id="status" name="status" style="width: 100%;">
                            <option value=""class="en"  data="no" id="subtitle-family"></option>
                            <!-- <option value="approve">Approve</option>
                            <option value="reject">Reject</option> -->
                        </select>
                        <div class="invalid-feedback" id="valid-day"></div>
                    </div>
                    <div class="form-group">
                        <label for="leave_deduction" class="en" data="leave_deduction" id="subtitle-family"></label> <sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control" id="leave_deduction" name="leave_deduction" placeholder="Enter notes..." autocomplete="off">
                        <div class="invalid-feedback" id="valid-leave_deduction"></div>
                    </div>

                </form>

            </div>
            <div class="modal-footer no-bd">
                <button type="button"  data="btn_close" id="btn-close" class="en btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i>
                    
                </button>
                <button type="button" id="btn-save" class="btn btn-info"
                  >
                    <i class="fas fa-check"></i>
                   
                </button>
            </div>
        </div>
    </div>
</div>

   <!-- Main Content -->
    <div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1 data="approve_leave" id="title-family" class="en"></h1>
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
                    <!-- <button class="btn btn-primary ml-auto" id="btn-add">
                            <i class="fas fa-plus-circle"></i>
                            Create Data
                        </button> -->
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover" id="user-table">
                            <thead class="thead-light">
                                <tr>
                                    <th><input type="checkbox" name="main_checkbox"><label></label></th>
                                    <th > <p class="en" data="no" id="subtitle-family"></p></th>
                                    <th ><p class="en" data="attachment" id="subtitle-family"></p></th>
                                    <th ><p class="en" data="user_name" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="reason" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="leave_type" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="from_date" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="to_date" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="type" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="duration" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="request_date" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="leave_dedution" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="status" id="subtitle-family"></p>  </th>
                                    <th><p class="en" data="action" id="subtitle-family"></p>   <button class="btn btn-sm btn-danger d-none" id="deleteAllBtn">Send to Accountant</button></th>

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
    // var langCode;
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
    // function onBtnLangClick() {
    // if (langCode === "en") {
    //     langCode = "kh";
    //     localStorage.setItem("lang-code", "kh")
    // } else {
    //     langCode = "en",
    //     localStorage.setItem("lang-code", "en")
    // }
    // checkLanguage();
    // }
    // document.getElementById("toggleLang").addEventListener("click", onBtnLangClick)
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
            ajax: "{{url('admin/approve/leaves')}}",
            columns: [{
                    data: 'checkbox',
                    name: 'checkbox',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'attachment',
                    name: 'attachment'
                },
                {
                    data: 'user.name',
                    name: 'user.name'
                },
                {
                    data: 'reason',
                    name: 'reason'
                },
                {
                    data: 'leavetype.leave_type',
                    name: 'leavetype.leave_type'
                },
                {
                    data: 'from_date',
                    name: 'from_date'
                },
                {
                    data: 'to_date',
                    name: 'to_date'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'number',
                    name: 'number'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'deduction_leave',
                    name: 'deduction_leave'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'approve_action',
                    name: 'approve_action'
                },
                // status=="pending"? {
                //     "data": null,
                //     render: function(data, type, row) {
                //         return `<div>  <button data-id="${row.id}" class="btn btn-sm btn-icon btn-info"  id="editBtn"><i class="fa fa-edit"></i></button>   <button data-id="${row.id}"  class="btn btn-sm btn-icon btn-danger" data-original-title="Delete"  id="deleteBtn"><i class="fa fa-trash-alt"></i></button></div>`
                //     }
                // }:
                // {
                //     "data": null,
                //     render: function(data, type, row) {
                //         return data.send_status == "false" ? `<div>  <button data-id="${row.id}" class="btn btn-sm btn-icon btn-info"  id="editBtn"><i class="fa fa-edit"></i></button>   <button data-id="${row.id}"  class="btn btn-sm btn-icon btn-danger" data-original-title="Delete"  id="deleteBtn"><i class="fa fa-trash-alt"></i></button></div>` : `<div>  <button data-id="${row.id}"  class="btn btn-sm btn-icon btn-danger" data-original-title="Delete"  id="deleteBtn"><i class="fa fa-trash-alt"></i></button></div>`
                //     }
                // },
                // {
                //     data: 'action',
                //     name: 'action',
                //     className: 'text-center',
                //     orderable: false,
                //     searchable: false
                // }
            ],
        }).on('draw', function() {
            $('input[name="country_checkbox"]').each(function() {
                this.checked = false;
            });
            $('input[name="main_checkbox"]').prop('checked', false);
            $('button#deleteAllBtn').addClass('d-none');
        });

        $('#user-table').DataTable().on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });



        // Store new company or update company
        $('#btn-save').click(function() {
            // save data state
            var formData = {
                status: $('#status').val(),
                leave_deduction: $('#leave_deduction').val(),

            };
            console.log(formData.status)

            var state = $('#btn-save').val();

            if (state == "update") {
                console.log(state);
                $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Updating...').attr("disabled", true);
                var id = $('#id').val();
                type = "PUT";
                ajaxurl = "{{ url('admin/approve/leaves/update') }}" + '/' + id;
                console.log(ajaxurl);
                console.log(id);
            }

            $.ajax({
                type: type,
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function(data) {
                    console.log(state);
                    if (state == "save") {
                        swal({
                            title: "Success!",
                            text: "Data has been added successfully!",
                            icon: "success",
                            timer: 3000
                        });

                        $('#user-table').DataTable().draw(false);
                        $('#user-table').DataTable().on('draw', function() {
                            $('[data-toggle="tooltip"]').tooltip();
                        });
                        // $("#company-form")[0].reset();
                    } else {
                        console.log(state);
                        console.log("Sinit");
                        // console.log(data.code);
                        if (data.code == 0) {
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


                        $('#user-table').DataTable().draw(false);
                        $('#user-table').DataTable().on('draw', function() {
                            $('[data-toggle="tooltip"]').tooltip();
                        });

                    }
                    // remove that from select value after save data to avoid dublicate data
                    // $('#user_id').find('option').remove().end().append('<option value="">Chooose employee</option>').val('');
                    // $('#timetable_id').find('option').remove().end().append('<option value="">Chooose time</option>').val('');
                    $('#formModal').modal('hide');
                },
                error: function(data) {

                    try {
                        if (state == "save") {

                            if (data.responseJSON.errors.status) {
                                $('#user_id').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-day').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-day').html(data.responseJSON.errors.status);
                            }
                            if (data.responseJSON.errors.leave_deduction) {
                                $('#leave_deduction').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-leave_deduction').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-leave_deduction').html(data.responseJSON.errors.leave_deduction);
                            }



                            $('#btn-save').html('<i class="fas fa-check"></i> Save Changes');
                            $('#btn-save').removeAttr('disabled');
                        } else {
                            if (data.responseJSON.errors.status) {
                                $('#user_id').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-day').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-day').html(data.responseJSON.errors.status);
                            }
                            if (data.responseJSON.errors.leave_deduction) {
                                $('#leave_deduction').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-leave_deduction').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-leave_deduction').html(data.responseJSON.errors.leave_deduction);
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
            $.get("{{ url('admin/approve/leaves/edit') }}" + '/' + id, function(data) {
                $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
                // show data on modal
                // $('#name').val(data.nip);
                $('#id').val(data.id);
                $('#leave_deduction').val(data.leave_deduction);
                var time = "";
                var status = ["pending", "approved", "rejected"]

                status.forEach((index) => {
                    if (data.status == index) {
                        console.log(index);
                    }
                    time = $('select[name="status"]')
                        .append(`<option value="${index}" ${index == data.status ? 'selected' : ''}>${index}</option>`)
                    //         // console.log(index)
                });
                $('#formModal').find('#status').append(time);
                // change value button save to update then state to save
                $('#btn-save').val('update').removeAttr('disabled');
                $('#formModal').modal('show');
                // $('.modal-title').html('Edit Data');
                $('#null').html('<small id="null">Kosongkan jika tidak ingin di ubah</small>');
                $('#btn-save').html('<i class="fas fa-check"></i> Edit');
                $('.close').click(function() {
                    $('#leave_deduction').val('');
                    $('#status').find('option').remove().end().append('<option value="">Choose Status</option>').val('');
                   
                })
                $('#btn-close').click(function() {
                    // remove that from select value after save data to avoid dublicate data
                    $('#leave_deduction').val('');
                    $('#status').find('option').remove().end().append('<option value="">Choose Status</option>').val('');

                })
            }).fail(function() {
                swal({
                    title: "Sorry!",
                    text: "Failed to update data!",
                    icon: "error",
                    timer: 3000
                });
            });
        });
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

            var url = '{{ url("admin/leave/updates") }}';

            if (checkedCountries.length > 0) {
                console.log('testing');
                $.post(url, {
                    countries_ids: checkedCountries
                }, function(data) {
                    console.log(data);
                    if (data.code == 0) {
                        $('#user-table').DataTable().draw(false);
                        $('#user-table').DataTable().on('draw', function() {
                            $('[data-toggle="tooltip"]').tooltip();
                        });

                        swal({
                            title: "Success!",
                            text: "Data has been update successfully!",
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
                }, 'json');

            }
        });
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
                            url: "{{ url('admin/leave/delete') }}" + '/' + id,
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
                            timer: 3000
                        });
                        break;
                }
            });
        });



       
    });
</script>
@endsection
