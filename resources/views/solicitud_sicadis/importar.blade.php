@extends('layouts.app')

@section('headSection')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">

@endsection


@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-layer-group" aria-hidden="true"></i>Solicitudes SICADI
                <!--<small>Create, Read, Update, Delete</small>-->
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('solicitud_sicadis.index') }}">Solicitudes</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            @include('includes.messages')
            <div class="row">
                <div class="col-xs-12">
                    <form action="{{ route('solicitud_sicadis.importprocess') }}" method="POST" enctype="multipart/form-data">
                    <div class="box">


                        {{ csrf_field() }}
                        Archivo CSV
                        <input type="file" name="archivoCSV" class="form-control" placeholder="">

                        <!-- /.box-body -->

                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                        <a href='{{ route('solicitud_sicadis.index') }}' class="btn btn-warning">Volver</a>
                    </div>

                    <!-- /.box -->
                    </form>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
@endsection
@section('footerSection')
    <!-- jQuery 3 -->
    <script src="{{ asset('bower_components/jquery/dist/jquery.min.js') }}"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="{{ asset('bower_components/bootstrap/dist/js/bootstrap.min.js') }}"></script>

    <!-- SlimScroll -->
    <script src="{{ asset('bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- page script -->

@endsection
