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

    .en[data="leave_out"]::before {
        content: 'Leave out'

    }

    .kh[data="leave_out"]::before {
        content: 'សុំចេញក្រៅ'
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
    .en[data="time_out"]::before {
        content: 'Time out'

    }
    .kh[data="time_out"]::before {
        content: 'ម៉ោងចេញ'
    }
     .en[data="date"]::before {
        content: 'Date'

    }
    .kh[data="date"]::before {
        content: 'ថ្ងៃ'
    }
    .en[data="time_in"]::before {
        content: 'Time In'

    }
    .kh[data="time_in"]::before {
        content: 'ម៉ោងចូល'
    }
    .en[data="arriving_time"]::before {
        content: 'Arriving Time'

    }
    .kh[data="arriving_time"]::before {
        content: 'ម៉ោងមកដល់'
    }
    #language-family{
        font-family:Khmer OS Battambang;
    }
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
                        <label for="status" class="en" data="status" id="subtitle-family"><sup class="text-danger">*</sup></label>
                        <select class="form-control select2" id="status" name="status" style="width: 100%;">
                            <option value="">Choose status</option>
                            <option value="approve">Approve</option>
                            <option value="reject">Reject</option>
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
            <h1 data="leave_out" id="title-family" class="en"></h1>
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
                                    <th > <p class="en" data="no" id="subtitle-family"></p></th>
                                    <th ><p class="en" data="user_name" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="reason" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="time_out" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="time_in" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="arriving_time" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="duration" id="subtitle-family"></p>  </th>
                                    <th ><p class="en" data="date" id="subtitle-family"></p>  </th>
                                    
                                    <th ><p class="en" data="status" id="subtitle-family"></p>  </th>
                                   
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

        // Initializing DataTable
        $('#user-table').DataTable({
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            ajax: "{{url('admin/approve/leaveouts')}}",
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
                    data: 'reason',
                    name: 'reason'
                },
                {
                    data: 'time_in',
                    name: 'time_in'
                },
                {
                    data: 'time_out',
                    name: 'time_out'
                },
                {
                    data: 'arrived_time',
                    name: 'arrived_time'
                },
                {
                    data: 'leave_out',
                    name: 'leave_out'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                // {
                //     data: 'leave_deduction',
                //     name: 'leave_deduction'
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
                //         return data.status == "pending" ? `<div>  <button data-id="${row.id}" class="btn btn-sm btn-icon btn-info"  id="editBtn"><i class="fa fa-edit"></i></button>   <button data-id="${row.id}"  class="btn btn-sm btn-icon btn-danger" data-original-title="Delete"  id="deleteBtn"><i class="fa fa-trash-alt"></i></button></div>` : `<div>  <button data-id="${row.id}"  class="btn btn-sm btn-icon btn-danger" data-original-title="Delete"  id="deleteBtn"><i class="fa fa-trash-alt"></i></button></div>`
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
        });

        $('#user-table').DataTable().on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });



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
                ajaxurl = "{{ url('admin/approve/leaveouts/update') }}" + '/' + id;
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
            $.get("{{ url('admin/approve/leaveouts/edit') }}" + '/' + id, function(data) {
                $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
                // show data on modal
                // $('#name').val(data.nip);
                $('#id').val(data.id);
                // $('#status').val(data.status);
                console.log(data.status);
                console.log(data.id);
                // change value button save to update then state to save
                $('#btn-save').val('update').removeAttr('disabled');
                $('#formModal').modal('show');
                $('.modal-title').html('Edit Data');
                $('#null').html('<small id="null">Kosongkan jika tidak ingin di ubah</small>');
                $('#btn-save').html('<i class="fas fa-check"></i> Edit');
            }).fail(function() {
                swal({
                    title: "Sorry!",
                    text: "Failed to update data!",
                    icon: "error",
                    timer: 3000
                });
            });
        });



        // Delete company
       
    });
</script>
@endsection