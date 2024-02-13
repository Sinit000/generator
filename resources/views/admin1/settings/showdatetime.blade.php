@extends('admin.layouts.master')
@section('title', 'Dashboard')

@section('css')
<!-- <link rel="stylesheet" href="https://demo.getstisla.com/assets/modules/fullcalendar/fullcalendar.min.css"> -->

@endsection

@section('content')
<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>DateTime</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item">
                    <a href="{{ url('admin/dashboard') }}">
                        <i class="fa fa-home"></i>
                        DateTime
                    </a>
                </div>
            </div>
        </div>
        @if (session('success'))
        <div class="alert alert-info alert-dismissible show fade">
            <div class="alert-body">
                <button class="close" data-dismiss="alert">
                    <span>×</span>
                </button>
                {!! session('success') !!}
            </div>
        </div>
        @endif
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <!-- <div class="card">
              <div class="card-header">
                <h4>Welcome to Attendance App</h4>
              </div>

            </div> -->
                    <div class="row">
                        @if($data->type_date_time == 'server' )
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12" id="btnServer">
                            <div class="card card-statistic-1 card-info">
                                <div class="card-icon  bg-info">
                                    <i class="fa fa-server" style="color: white;font-size:30px;"></i>
                                </div>
                                <div class="card-wrap">
                                    <form action="" method="">
                                        @csrf
                                        <input type="hidden" name="cid" value="{{$data->id}}" id="cid">
                                        <input type="hidden" name="type_date_time" value="server" id="type_date_time">
                                        <div class="card-header">
                                            <h4>Server date</h4>
                                            <div class="row" style="padding-top: 10px;">
                                                <p style="color: red;">Running</p>
                                            </div>

                                        </div>
                                        <div class="card-body">

                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>

                        @else

                        <div class="col-lg-3 col-md-6 col-sm-6 col-12" id="btnServer">
                            <div class="card card-statistic-1">
                                <div class="card-icon  bg-secondary">
                                    <i class="fa fa-server" style="color: white;font-size:30px;"></i>
                                </div>
                                <div class="card-wrap">
                                    <form action="" method="">
                                        @csrf
                                        <input type="hidden" name="cid" value="{{$data->id}}" id="cid">
                                        <input type="hidden" name="type_date_time" value="server" id="type_date_time">
                                        <div class="card-header">
                                            <h4>Server date</h4>
                                            <h4></h4>

                                        </div>
                                        <div class="card-body">

                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>

                        @endif
                        @if($data->type_date_time != 'server' )
                        
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12" id="btnComputer">
                            <div class="card card-statistic-1 card-info">

                                <div class="card-icon bg-info">
                                    <i class="fa fa-desktop" style="font-size: 30px;color:white;"></i>
                                </div>
                                <div class="card-wrap card-info">
                                    <form method="POST" action="">
                                        @csrf
                                        <input type="hidden" name="cid" value="{{$data->id}}" id="cid">
                                        <input type="hidden" name="type_date_time" value="computer" id="type_date_time">
                                        <div class="card-header">
                                            <h4>Comuter date</h4>
                                            <h4> </h4>
                                            
                                           
                                            <div class="row" style="padding-top: 10px;">
                                                <p style="color: red;">Running</p>
                                            </div>
                                           
                                        </div>
                                        <div class="card-body">
                                    </form>


                                    


                                </div>
                            </div>
                        </div>
                        @else
                        
                        <div class="col-lg-3 col-md-6 col-sm-6 col-12" id="btnComputer">
                            <div class="card card-statistic-1 ">

                                <div class="card-icon bg-secondary">
                                    <i class="fa fa-desktop" style="font-size: 30px;color:white;"></i>
                                </div>
                                <div class="card-wrap card-info">
                                    <form method="POST" action="">
                                        @csrf
                                        <input type="hidden" name="cid" value="{{$data->id}}" id="cid">
                                        <input type="hidden" name="type_date_time" value="computer" id="type_date_time">
                                        <div class="card-header">
                                            <h4>Comuter date</h4>
                                            <h4> </h4>
                                            
                                         
                                            <div class="row" style="padding-top: 10px;">
                                               
                                            </div>
                                            
                                        </div>
                                        <div class="card-body">
                                    </form>


                                   


                                </div>
                            </div>
                        </div>
                        @endif

                    </div>


                </div>
            </div>
        </div>
</div>
</section>
</div>
@endsection
@section('js')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // key up function on form password
        $('#btnServer').click(function() {
            var id = $('#cid').val()
            console.log(id);
            var formData = {
                type_date_time: "server"
            };
            $.ajax({
                type: "POST",
                url: "{{url('admin/datetime/update')}}",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    console.log('server success')
                    var url = "{{URL::to('admin/datetime')}}" ;
                    window.location.assign(url)
                },
                error: function(data) {

                }
            });
        })
        $('#btnComputer').click(function() {
            
            var formData = {
                type_date_time: "computer"
            };
            console.log('ttt');
            $.ajax({
                type: "POST",
                url: "{{url('admin/datetime/update')}}",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    console.log('pc success')
                    var url = "{{URL::to('admin/datetime')}}" ;
                    window.location.assign(url)
                },
                error: function(data) {

                }
            });
        })
        // $('form').submit(function() {
        //     $('#btnServer').html('<i class="fas fa-cog fa-spin"></i> Saving...').attr("disabled", true);

        // });
        // $('form').submit(function() {
        //     $('#btnComputer').html('<i class="fas fa-cog fa-spin"></i> Saving...').attr("disabled", true);
        //     var id = $('#cid').val()
        //     console.log(id);
        // });
        //  $('#addform').on('submit', function(e) {
        //         e.preventDefault();
        //         var form = this;
        //         console.log("adding");
        //         $.ajax({
        //             url: $(form).attr('action'),
        //             method: $(form).attr('method'),
        //             data: new FormData(form),
        //             processData: false,
        //             dataType: 'json',
        //             contentType: false,
        //             beforeSend: function() {
        //                 $(form).find('span.error-text').text('');
        //             },
        //             success: function(data) {
        //                 if (data.code == 1) {
        //                     console.log(data);

        //                     toastr.error(data.message);
        //                     console.log("toast1");

        //                 } else {
        //                     $(form)[0].reset();
        //                     //  alert(data.msg);
        //                     // $('#mytable').DataTable().ajax.reload(null, false);
        //                     // $('.addModal').modal('hide');
        //                     console.log("toast2");
        //                     toastr.success(data.message);
        //                     //location.href = "/products";
        //                     console.log("succee");
        //                 }
        //             }
        //         });
        // });

    });
</script>
@endsection