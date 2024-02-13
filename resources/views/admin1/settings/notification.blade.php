@extends('admin.layouts.master')
@section('title', 'Notification')

@section('css')
<link rel="stylesheet" href="{{ asset('backend/modules/datatables/datatables.min.css') }}">
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
                    <!-- <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">From date <sup class="text-danger">*</sup></label>
                                <div class="bootstrap-timepicker ">
                                    <input type="date" class="form-control datepicker" id="from_date" name="from_date">
                                </div>
                                <div class="invalid-feedback" id="valid-from_date"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">To date <sup class="text-danger">*</sup></label>
                                <div class="bootstrap-timepicker ">
                                    <input type="date" class="form-control datepicker" id="to_date" name="to_date">
                                </div>
                                <div class="invalid-feedback" id="valid-to_date"></div>
                            </div>
                        </div>

                    </div> -->
                    <div class="form-group">
                        <label for="nip">Title <sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter title..." autocomplete="off">
                        <div class="invalid-feedback" id="valid-title"></div>
                    </div>

                    <div class="form-group">
                        <label for="name">Body <sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control" id="body" name="body" placeholder="Enter body..." autocomplete="off">
                        <div class="invalid-feedback" id="valid-body"></div>
                    </div>
                    <div class="form-group">
                        <label for="name">Date</label>
                        <div class="bootstrap-timepicker">
                            <input type="date" class="form-control datepicker" id="date" name="date">
                        </div>

                        <!-- <div class="invalid-feedback" id="valid-mydate1"></div> -->
                    </div>
                    <div class="form-group">
                        <label for="on_duty_time">Time </label>
                        <div class="bootstrap-timepicker">
                            <input type="time" class="form-control timepicker" id="time" name="time">
                        </div>
                        <!-- <div class="invalid-feedback" id="valid-time"></div> -->
                    </div>
                    <div class="form-group">
                        <label for="workday_id">To specific employee </label>
                        <select class="form-control select2" id="user_id" name="user_id" style="width: 100%;">
                            <option value="">Choose employee</option>
                        </select>
                        <!-- <div class="invalid-feedback" id="valid-day"></div> -->
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
            <h1>Notification Data</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ url('admin/dashboard') }}">
                        <i class="fa fa-home"></i>
                        Dashboard
                    </a>
                </div>
                <div class="breadcrumb-item">
                    <i class="fas fa-user"></i>
                    Notification List
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card card-info">
                <div class="card-header">
                    <button class="btn btn-info ml-auto" id="btn-add">
                        <i class="fas fa-plus-circle"></i>
                        Create Data
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover table-bordered" id="user-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Title</th>
                                    <th>Body</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>To Employee</th>
                                    <th>Status</th>
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
            ajax: "{{url('admin/notification')}}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'title',
                    name: 'title'
                },
                {
                    data: 'body',
                    name: 'body'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'time',
                    name: 'time'
                },
                {
                    data: 'username',
                    name: 'username'
                },
                {
                    data: 'status',
                    name: 'status'
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
                    // className: 'text-center',
                    // orderable: false,
                    // searchable: false
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
                url: "{{url('admin/notification/componet')}}",
                // data:"",
                dataType: "json",
                success: function(response) {
                    // console.log(response.data);
                    //         console.log(response);
                    if (response.status == 404) {


                    } else {

                        // console.log(response.data)
                        var selOpts = "";
                        for (i = 0; i < response.data.length; i++) {

                            var id = response.data[i]['id'];
                            var val = response.data[i]['name'];
                            selOpts += "<option value='" + id + "'>" + val + "</option>";

                        }

                        $('#user_id').append(selOpts);

                        // timetable

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
                console.log('close button');
                $('#user_id').find('option').remove().end().append('<option value="">Chooose employee</option>').val('');

            })
            $('#btn-close').click(function() {
                // remove that from select value after save data to avoid dublicate data
                console.log('close button');
                $('#user_id').find('option').remove().end().append('<option value="">Chooose employee</option>').val('');

            })
        });

        // Store new company or update company
        $('#btn-save').click(function() {
            // save data state
            var formData = {
                title: $('#title').val(),
                body: $('#body').val(),
                user_id: $('#user_id').val(),
                date: $('#date').val(),
                time: $('#time').val(),

            };

            var state = $('#btn-save').val();

            var type = "POST";
            var ajaxurl = "{{url('admin/notification/store')}}";
            $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Saving...').attr("disabled", true);
            console.log(state);
            // update state
            if (state == "update") {
                console.log(state);
                $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Updating...').attr("disabled", true);
                var id = $('#id').val();
                type = "PUT";
                ajaxurl = "{{ url('admin/notification/update') }}" + '/' + id;
                console.log(ajaxurl);
            }

            $.ajax({
                type: type,
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function(data) {
                    console.log(data.status);
                    console.log(data.checktime);
                    // console.log(data.date_today)
                    if (state == "save") {
                        if (data.code == 0) {
                            swal({
                                title: "Success!",
                                text: "Data has been added successfully!",
                                icon: "success",
                                timer: 3000
                            });
                        } else {
                            swal({
                                title: "Success!",
                                text: "Data has been added successfully!",
                                icon: "success",
                                timer: 3000
                            });
                        }


                        $('#user-table').DataTable().draw(false);
                        $('#user-table').DataTable().on('draw', function() {
                            $('[data-toggle="tooltip"]').tooltip();
                        });
                    } else {
                        console.log(data);
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

                    $('#user_id').find('option').remove().end().append('<option value="">Chooose employee</option>').val('');

                    $('#formModal').modal('hide');
                },
                error: function(data) {
                    console.log(data);
                    try {
                        if (state == "save") {
                            if (data.responseJSON.errors.title) {
                                $('#title').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-title').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-title').html(data.responseJSON.errors.title);
                            }
                            if (data.responseJSON.errors.body) {
                                $('#body').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-body').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-body').html(data.responseJSON.errors.body);
                            }


                            $('#btn-save').html('<i class="fas fa-check"></i> Save Changes');
                            $('#btn-save').removeAttr('disabled');
                        } else {
                            if (data.responseJSON.errors.title) {
                                $('#title').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-title').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-title').html(data.responseJSON.errors.title);
                            }
                            if (data.responseJSON.errors.body) {
                                $('#body').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-body').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-body').html(data.responseJSON.errors.body);
                            }


                            $('#btn-save').html('<i class="fas fa-check"></i> Update');
                            $('#btn-save').removeAttr('disabled');
                        }
                    } catch {
                        console.log('error from server');
                        console.log(data);
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
            $.get("{{ url('admin/notification/edit') }}" + '/' + id, function(data) {
                console.log(data.data.id);
                $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
                // show data on modal
                // $('#name').val(data.nip);
                $('#id').val(data.data.id);

                // console.log(data.id);
                $('#title').val(data.data.title);
                $('#body').val(data.data.body);
                $('#time').val(data.data.time);
                $('#date').val(data.data.date);
                $.each(data.user, function(key, value) {

                    selOpts = $('select[name="user_id"]').append(`<option value="${value.id}" ${value.id == data.data.user_id? 'selected' : ''}>${value.name}</option>`)
                });
                // $('.editModal').append(selOpts);
                $('#formModal').find('#user_id').append(selOpts)
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
                            url: "{{ url('admin/notification/delete') }}" + '/' + id,
                            success: function(data) {
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