@extends('admin.layouts.master')
@section('title', 'Approve')

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

                    <div class="form-group">
                        <label for="status">Status <sup class="text-danger">*</sup></label>
                        <select class="form-control select2" id="status" name="status" style="width: 100%;">
                            <option value="">Choose status</option>
                            <option value="approved">Approve</option>
                            <option value="rejected">Reject</option>
                        </select>
                        <div class="invalid-feedback" id="valid-status"></div>
                    </div>


                </form>

            </div>
            <div class="modal-footer no-bd">
                <button type="button" id="btn-close" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Close
                </button>
                <button type="button" id="btn-save" class="btn btn-info">
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
            <h1>Overtime Compensation</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ url('admin/dashboard') }}">
                        <i class="fa fa-home"></i>
                        Dashboard
                    </a>
                </div>
                <div class="breadcrumb-item">
                    <i class="fas fa-user"></i>
                    Approve List
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
                                    <th>No</th>
                                    <th>Employee Name </th>
                                    <th>Request Type</th>
                                    <th>Reason </th>
                                    <th>From Date </th>
                                    <th>To Date </th>
                                    <th>type</th>
                                    <th>Duration</th>
                                    <!-- <th>Approve By</th> -->
                                    <th>Request Date</th>
                                    <!-- <th>Request Date </th>
                                        <th>Leave Deduction</th> -->
                                    <th>Status </th>
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
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            ajax: "{{url('admin/approve/overtimecompesations')}}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'user.name',
                    name: 'user.name'
                },
                {
                    data: 'request_type',
                    name: 'request_type'
                },
                {
                    data: 'reason',
                    name: 'reason'
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
                    data: 'duration',
                    name: 'duration'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                // {
                //     data: 'approved_by',
                //     name: 'approved_by'
                // },
                {
                    data: 'status',
                    name: 'status'
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
                //         return data.status == "approved" ? `<div>  <button data-id="${row.id}" class="btn btn-sm btn-icon btn-info"  id="editBtn"><i class="fa fa-edit"></i></button>   <button data-id="${row.id}"  class="btn btn-sm btn-icon btn-danger" data-original-title="Delete"  id="deleteBtn"><i class="fa fa-trash-alt"></i></button></div>` : `<div>  <button data-id="${row.id}"  class="btn btn-sm btn-icon btn-danger" data-original-title="Delete"  id="deleteBtn"><i class="fa fa-trash-alt"></i></button></div>`
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


    //    var $id=0;
        // Store new company or update company
        $('#btn-save').click(function() {
            // save data state
            var formData = {
                status: $('#status').val(),
                // leave_deduction: $('#leave_deduction').val(),

            };
            console.log(formData.status)

            var state = $('#btn-save').val();
            console.log(state);
            console.log('testing');

            if (state == "update") {
                console.log(state);
                $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Updating...').attr("disabled", true);
                var id = $('#id').val();
                type = "PUT";
                ajaxurl = "{{ url('admin/approve/overtimecompesations/update') }}" + '/' + id;
                console.log(ajaxurl);
                console.log(id);
            }

            $.ajax({
                type: type,
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                success: function(data) {
                    console.log('success');
                    console.log(data.code);
                    console.log(data.total_duration);
                    // console.log(data.left_duration);
                    // console.log('duration');
                    console.log(data.case);
                    console.log(data.userDuration);
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
                    // remove that from select value after save data to avoid dublicate data
                    // $('#user_id').find('option').remove().end().append('<option value="">Chooose employee</option>').val('');
                    // $('#timetable_id').find('option').remove().end().append('<option value="">Chooose time</option>').val('');
                    $('#formModal').modal('hide');
                },
                error: function(data) {

                    try {
                        if (state == "save") {

                            if (data.responseJSON.errors.status) {
                                $('#status').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-status').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-status').html(data.responseJSON.errors.status);
                            }
                           



                            $('#btn-save').html('<i class="fas fa-check"></i> Save Changes');
                            $('#btn-save').removeAttr('disabled');
                        } else {
                            if (data.responseJSON.errors.status) {
                                $('#status').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-status').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-status').html(data.responseJSON.errors.status);
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
            // assing id to hiddden input
            $('#id').val(id);
            $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
                // show data on modal
                // $('#name').val(data.nip);
                // $('#id').val(data.id);
                // // $('#status').val(data.status);
                // console.log(data.status);
                // console.log(data.id);
                // change value button save to update then state to save
                $('#btn-save').val('update').removeAttr('disabled');
                $('#formModal').modal('show');
                $('.modal-title').html('Edit Data');
                $('#null').html('<small id="null">Kosongkan jika tidak ingin di ubah</small>');
                $('#btn-save').html('<i class="fas fa-check"></i> Edit');
            // $.get("{{ url('admin/approve/overtimecompesations/edit') }}" + '/' + id, function(data) {
            //     $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
            //     // show data on modal
            //     // $('#name').val(data.nip);
            //     $('#id').val(data.id);
            //     // $('#status').val(data.status);
            //     console.log(data.status);
            //     console.log(data.id);
            //     // change value button save to update then state to save
            //     $('#btn-save').val('update').removeAttr('disabled');
            //     $('#formModal').modal('show');
            //     $('.modal-title').html('Edit Data');
            //     $('#null').html('<small id="null">Kosongkan jika tidak ingin di ubah</small>');
            //     $('#btn-save').html('<i class="fas fa-check"></i> Edit');
            // }).fail(function() {
            //     swal({
            //         title: "Sorry!",
            //         text: "Failed to update data!",
            //         icon: "error",
            //         timer: 3000
            //     });
            // });
        });



        // Delete company
       
    });
</script>
@endsection