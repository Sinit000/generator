@extends('admin.layouts.master')
@section('title', 'Counter')

@section('css')
    <link rel="stylesheet" href="{{ asset('backend/modules/datatables/datatables.min.css') }}">
@endsection

@section('content')
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nip">OT Duration </label>
                                <input type="text" class="form-control" id="ot_duration" name="ot_duration"
                                    placeholder="Enter Duration..." autocomplete="off">
                                <!-- <div class="invalid-feedback" id="valid-name"></div> -->
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="no">Total PH </label>
                                <input type="text" class="form-control" id="total_ph" name="total_ph"
                                    placeholder="Enter total h..." autocomplete="off">
                                <!-- <div class="invalid-feedback" id="valid-notes"></div> -->
                            </div>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="off_day">Hospitality Leave </label>
                            <input type="text" class="form-control" id="hospitality_leave" name="hospitality_leave"
                                placeholder="Enter hospitality leave..." autocomplete="off">
                            <!-- <div class="invalid-feedback" id="valid-off_day"></div> -->
                        </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="name">Marriage Leave </label>
                            <input type="text" class="form-control" id="marriage_leave" name="marriage_leave"
                                placeholder="Enter marriage leave..." autocomplete="off">
                        </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Peternity Leave </label>
                                <input type="text" class="form-control" id="peternity_leave" name="peternity_leave"
                                    placeholder="Enter peternity leave..." autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                            <label for="name">Funeral Leave </label>
                            <input type="text" class="form-control" id="funeral_leave" name="funeral_leave"
                                placeholder="Enter funeral leave..." autocomplete="off">
                        </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name">Maternity Leave </label>
                        <input type="text" class="form-control" id="maternity_leave" name="maternity_leave"
                            placeholder="Enter maternity leave..." autocomplete="off">
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
                <h1>Employee Counter</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item">
                        <a href="{{ url('admin/dashboard') }}">
                            <i class="fa fa-home"></i>
                            Dashboard
                        </a>
                    </div>
                    <div class="breadcrumb-item">
                        <i class="fas fa-user"></i>
                       Counter List
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
                                        <th>Position</th>
                                        <th>Total OT </th>
                                        <th>Total PH </th>
                                        <th>Hospitality Leave</th>
                                        <th>Marriage Leave</th>
                                        <th>Peternity Leave</th>
                                        <th>Funeral Leave</th>
                                        <th>Maternity Leave</th>
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
                ajax: "{{url('admin/counter')}}",
                columns: [
                    {
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
                        data: 'user.position.position_name',
                        name: 'user.position.position_name'
                    },
                    {
                        data: 'ot_duration',
                        name: 'ot_duration'
                    },
                    {
                        data: 'total_ph',
                        name: 'total_ph'
                    },
                    {
                        data: 'hospitality_leave',
                        name: 'hospitality_leave'
                    },
                    {
                        data: 'marriage_leave',
                        name: 'marriage_leave'
                    },
                    {
                        data: 'peternity_leave',
                        name: 'peternity_leave'
                    },
                    {
                        data: 'funeral_leave',
                        name: 'funeral_leave'
                    },
                    {
                        data: 'maternity_leave',
                        name: 'maternity_leave'
                    },
                    {
                        data: 'action'
                    }
                    
                   
                ],
            });

            $('#user-table').DataTable().on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            // Store new company or update company
            $('#btn-save').click(function() {
                // save data state
                // send to backend
                var formData = {
                    ot_duration: $('#ot_duration').val(),
                    total_ph: $('#total_ph').val(),
                    hospitality_leave: $('#hospitality_leave').val(),
                    marriage_leave: $('#marriage_leave').val(),
                    peternity_leave: $('#peternity_leave').val(),
                    funeral_leave: $('#funeral_leave').val(),
                    maternity_leave: $('#maternity_leave').val(),
                };

                var state = $('#btn-save').val();


                // var type = "POST";
                // var ajaxurl = "{{url('admin/counter/store')}}";
                $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Saving...').attr("disabled", true);
                console.log(state);
                var id = $('#id').val();
                // update state
                if (state == "update") {
                    console.log(state);
                    $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Updating...').attr("disabled", true);
                    
                    
                }

                $.ajax({
                    type: "PUT",
                    url: "{{ url('admin/counter/update') }}" + '/' + id,
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        console.log(data);
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

                        $('#formModal').modal('hide');
                    },
                    error: function(data) {
                        try {
                           console.log(data.responseText);
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
            // // edit 
            //  Edit Category
            $(document).on('click', '#editBtn', function() {
                var id= $(this).attr('data-id');
                console.log(id);
                $.get("{{ url('admin/counter/edit') }}" + '/' + id , function(data) {
                    $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
                    // show data on modal
                    // $('#name').val(data.nip);
                    console.log(data);
                    $('#id').val(data.id);
                    $('#ot_duration').val(data.ot_duration);
                    $('#total_ph').val(data.total_ph);
                    $('#hospitality_leave').val(data.hospitality_leave);
                    $('#marriage_leave').val(data.marriage_leave);
                    $('#peternity_leave').val(data.peternity_leave);
                    $('#funeral_leave').val(data.funeral_leave);
                    $('#maternity_leave').val(data.maternity_leave);
                    
                                   //                       // timetable
                                  
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



            // // Delete company
            // $(document).on('click', '#deleteBtn', function(){
            //     var id= $(this).attr('data-id');
            //     console.log(id);
            //     swal("Warning!", "Are you sure you want to delete?", "warning", {
            //         buttons: {
            //             cancel: "NO!",
            //             ok: {
            //                 text: "Yes!",
            //                 value: "ok"
            //             }
            //         },
            //     }).then((value) => {
            //         switch (value) {
            //             case "ok" :
            //                 $.ajax({
            //                     type: "DELETE",
            //                     url: "{{ url('admin/location/delete') }}" + '/' + id,
            //                     success: function(data) {
            //                         if(data.code == 0){
            //                             $('#user-table').DataTable().draw(false);
            //                             $('#user-table').DataTable().on('draw', function() {
            //                                 $('[data-toggle="tooltip"]').tooltip();
            //                             });

            //                             swal({
            //                                 title: "Success!",
            //                                 text: data.message,
            //                                 icon: "success",
            //                                 timer: 3000
            //                             });
            //                         }else{
            //                             swal({
            //                             title: "Sorry!",
            //                             text: data.message,
            //                             icon: "error",
            //                             timer: 3000
            //                         });
            //                         }
            //                     },
            //                     error: function(data) {
            //                         swal({
            //                             title: "Sorry!",
            //                             text: "An error occurred, please try again",
            //                             icon: "error",
            //                             timer: 3000
            //                         });
            //                     }
            //                 });
            //             break;

            //             default :
            //                 swal({
            //                     title: "Oh Ya!",
            //                     text: "Data is not change",
            //                     icon: "info",
            //                     timer: 2
            //                 });
            //             break;
            //         }
            //     });
            // });
            
            
        });
    </script>
@endsection
