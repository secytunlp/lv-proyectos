@extends('layouts.app')
@section('headSection')

    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
<style>
    /* Quitar viñetas de la lista desplegable */
    .ui-autocomplete {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 200px; /* Ajustar la altura máxima de la lista */
        overflow-y: auto;  /* Agregar desplazamiento vertical si es necesario */
        z-index: 1000;     /* Asegurar que la lista se muestre encima de otros elementos */
    }

    /* Estilo de los elementos de la lista */
    .ui-menu-item {
        padding: 10px;
        border-bottom: 1px solid #ccc;
        background-color: #fff;
    }

    /* Estilo de los elementos cuando se hace hover o se seleccionan */
    .ui-menu-item:hover, .ui-menu-item.ui-state-focus {
        background-color: #f0f0f0;
    }
</style>

@endsection


@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-user-friends" aria-hidden="true"></i>SICADI
                <small>Rectificar Solicitud</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('solicitud_sicadis.index') }}">Solicitudes SICADI</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">



                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('solicitud_sicadis.saveRect',$solicitud_sicadi->id) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="box-body">
                                @include('includes.messages')
                                <!-- Nav tabs -->






                                        <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{Form::label('apellido', 'Apellido')}}
                                                {{Form::text('apellido', $solicitud_sicadi->apellido, ['class' => 'form-control','placeholder'=>'Apellido','disabled'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{Form::label('nombre', 'Nombre')}}
                                                {{Form::text('nombre', $solicitud_sicadi->nombre, ['class' => 'form-control','placeholder'=>'Nombre','disabled'])}}
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('cuil', 'CUIL')}}
                                                {{Form::text('cuil', $solicitud_sicadi->cuil, ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X','disabled'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                {{Form::label('email', 'Email')}}
                                                {{Form::email('email', $solicitud_sicadi->email_institucional, ['class' => 'form-control','placeholder'=>'Email','disabled'])}}
                                            </div>
                                        </div>

                                    </div>


                                <div class="row">

                                    <div class="col-md-8">

                                        <div class="form-group">
                                            {{Form::label('comentarios', 'Motivos')}}
                                            {{Form::textarea('comentarios', '', ['class' => 'form-control'])}}

                                        </div>
                                    </div>
                                </div>




                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href="{{ route('solicitud_sicadis.index') }}" class="btn btn-warning">Volver</a>

                                    </div>

                            </div>
                        </form>
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col-->
            </div>

            <!-- ./row -->
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
    <!-- Select2 -->
    <script src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <!-- Inputmask -->
    <script src="{{ asset('bower_components/inputmask/dist/min/jquery.inputmask.bundle.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script src="{{ asset('dist/js/confirm-exit.js') }}"></script>
    <!-- page script -->


@endsection
