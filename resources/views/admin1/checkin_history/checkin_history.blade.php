@extends('admin.layouts.master')
@section('title', 'Attendance')

@section('css')
<link rel="stylesheet" href="{{ asset('backend/modules/datatables/datatables.min.css') }}">
@endsection

@section('content')
<!-- Modal -->


<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Checkin Histories</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="">
                        <i class="fa fa-home"></i>
                        Dashboard
                    </a>
                </div>
                <div class="breadcrumb-item">
                    <i class="fas fa-user"></i>
                    Checkin List
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
                        <table class="table table-sm table-hover table-bordered" id="user-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>No</th>
                                    <th>Employee Name </th>
                                    
                                    <th>Checkin time </th>
                                    <th>Checkin Status </th>
                                    <th>Late</th>
                                    <th>Checkout time </th>
                                    <th>Checkout Status</th>
                                    <th>Early</th>
                                    <th>Status</th>
                                    <th>Attendance Date</th>
                                    <th>Duration</th>
                                    <th>Edit Date </th>
                                    <th>Note</th>

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
            ajax: "{{url('admin/checkins/histories')}}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'checkin.user.name',
                    name: 'checkin.user.name'
                },
                {
                    data: 'checkin_time',
                    name: 'checkin_time'
                },
                {
                    data: 'checkin_status',
                    name: 'checkin_status'
                },
                {
                    data: 'checkin_late',
                    name: 'checkin_late'
                },
                {
                    data: 'checkout_time',
                    name: 'checkout_time'
                },
                {
                    data: 'checkout_status',
                    name: 'checkout_status'
                },
                {
                    data: 'checkout_late',
                    name: 'checkout_late'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'duration',
                    name: 'duration'
                },
                {
                    data: 'edit_date',
                    name: 'edit_date'
                },
                {
                    data: 'note',
                    name: 'note'
                },

               
            ],
        });


        $('#user-table').DataTable().on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        






    });
    // get qr
</script>
@endsection