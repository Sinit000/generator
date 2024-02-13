@extends('admin.layouts.master')
@section('title', 'Holiday')

@section('css')
<link rel="stylesheet" href="{{ asset('public/backend/modules/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('public/backend/css/bootstrap-datepicker.standalone.min.css') }}">
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
                <form id="company-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="form-group">
                        <label for="nip">Name <sup class="text-danger">*</sup></label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Enter name..." autocomplete="off">
                        <div class="invalid-feedback" id="valid-name"></div>
                    </div>
                    <div class="form-group">
                        <label for="name">File </label>
                        <input type="file" class="form-control" id="file" name="file" placeholder="Upload file..." autocomplete="off">
                        <!-- <div class="invalid-feedback" id="valid-notes"></div> -->
                    </div>
                    
                    <div class="form-group">
                        <label for="name">File Url </label>
                        <input type="text" class="form-control" id="file_url" name="file_url" placeholder="Enter url..." autocomplete="off">
                        <!-- <div class="invalid-feedback" id="valid-notes"></div> -->
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
            <h1>Holiday Data</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ url('admin/dashboard') }}">
                        <i class="fa fa-home"></i>
                        Dashboard
                    </a>
                </div>
                <div class="breadcrumb-item">
                    <i class="fas fa-user"></i>
                    Holiday List
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
                                    <th>Name</th>
                                    <th>QR</th>

                                    <!-- <th>From Date</th>
                                    <th>To Date</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Notes</th> -->
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
<script src="{{ asset('public/backend/modules/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('public/backend/modules/sweetalert/sweetalert.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/js/jquery-ui.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('public/backend/js/moment.min.js') }}"></script>

<script type="text/javascript" src="{{ asset('public/backend/js/printThis.js')}}"></script>
<script src="https://cdn.rawgit.com/davidshimjs/qrcodejs/gh-pages/qrcode.min.js"></script>


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
            ajax: "{{url('admin/qrs')}}",
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
                    data: 'qr',
                    name: 'qr'
                },
                // {
                //     data: 'from_date',
                //     name: 'from_date'
                // },
                // {
                //     data: 'to_date',
                //     name: 'to_date'
                // },
                // {
                //     data:'duration',
                //     name:'duration'
                // },
                // {
                //     data: 'holiday_status',
                //     name: 'holiday_status'
                // },
                // {
                //     data: 'notes',
                //     name: 'notes'
                // },
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
            $('.modal-title').html('Create Data');
            $('#company-form').trigger('reset');
            $('#btn-save').html('<i class="fas fa-check"></i> Save Data');
            $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
            $('#btn-save').val('save').removeAttr('disabled');
        });

        // Store new company or update company
        $('#btn-save').click(function() {
            // const form = document.getElementById('company-form');
            // var formData = new FormData(form);
            // var file= $('#file').val();
            // var name = $('#name').val();
            // var url = $('#file_url').val();
            // console.log(name);
            // console.log(url);

            // formData.append('name',name),
            // formData.append('file_url',url);
            // if (file) {
            //     formData.append('file', $('#file')[0].files[0]);
               
            // } else {
            //     console.log('no file');
            // }
            // // save data state
            var formData = {
                name: $('#name').val(),
                file_url: $('#file_url').val(),
                file: $('#file').val(),
                
            };

            var state = $('#btn-save').val();

            var type = "POST";
            var ajaxurl = "{{url('admin/qrs/store')}}";
            $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Saving...').attr("disabled", true);
            console.log(state);
            // update state
            if (state == "update") {
                console.log(state);
                $('#btn-save').html('<i class="fas fa-cog fa-spin"></i> Updating...').attr("disabled", true);
                var id = $('#id').val();
                type = "PUT";
                ajaxurl = "{{ url('admin/qrs/update') }}" + '/' + id;
                console.log(ajaxurl);
            }

            $.ajax({
                type: type,
                // headers: {
                //     'X-CSRF-TOKEN': '{{ csrf_token() }}'
                // },
                // mimeType: "multipart/form-data",
                url: ajaxurl,
                data: formData,
                dataType: 'json',
                // processData: false,
                // contentType: false,
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
                            console.log(data)
                            if (data.responseJSON.errors.name) {
                                $('#name').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-name').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-name').html(data.responseJSON.errors.name);
                            }
                            if (data.responseJSON.errors.from_date) {
                                $('#from_date').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-mydate').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-mydate').html(data.responseJSON.errors.from_date);
                            }
                            if (data.responseJSON.errors.to_date) {
                                $('#to_date').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-mydate1').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-mydate1').html(data.responseJSON.errors.to_date);
                            }

                            $('#btn-save').html('<i class="fas fa-check"></i> Save Changes');
                            $('#btn-save').removeAttr('disabled');
                        } else {
                            if (data.responseJSON.errors.name) {
                                $('#name').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-name').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-name').html(data.responseJSON.errors.name);
                            }
                            if (data.responseJSON.errors.from_date) {
                                $('#from_date').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-mydate').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-mydate').html(data.responseJSON.errors.from_date);
                            }
                            if (data.responseJSON.errors.to_date) {
                                $('#to_date').removeClass('is-valid').addClass('is-invalid');
                                $('#valid-mydate1').removeClass('valid-feedback').addClass('invalid-feedback');
                                $('#valid-mydate1').html(data.responseJSON.errors.to_date);
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
                                text: "An error occurred, please try again Silahkan coba lagi",
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
            $.get("{{ url('admin/qrs/edit') }}" + '/' + id, function(data) {
                $('#company-form').find('.form-control').removeClass('is-invalid is-valid');
                // show data on modal
                // $('#name').val(data.nip);
                $('#id').val(data.id);
                $('#name').val(data.name);
                // $('#from_date').val(data.from_date);
                // $('#to_date').val(data.to_date);
                // $('#status').val(data.status);
                // $('#notes').val(data.notes);
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
                            url: "{{ url('admin/qrs/delete') }}" + '/' + id,
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
        function myfucntion() {
            display_c7();
        //    alert('hi sinit');
            console.log('sinit');
        }

        function display_c7() {
            // one day
            // var refresh = 86400000;
            // refresh one hour
            var refresh = 3600000 // Refresh rate in milli seconds
            mytime = setTimeout('myfucntion()', refresh)
        }
        display_c7();
</script>
@endsection