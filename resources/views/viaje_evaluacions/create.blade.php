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
                <i class="fa fa-th-list" aria-hidden="true"></i>Evaluación Viajes/Estadías
                <small>Asignar Evaluadores</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('viajes.index') }}">Viajes/Estadías</a></li>
                <li><a href="{{ route('viaje_evaluacions.index') }}">Evaluaciones</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">@if($viaje) Solicitud {{ $viaje->periodo->nombre }} - {{ $viaje->investigador->persona->apellido }} {{ $viaje->investigador->persona->nombre }}@endif</h3>
                        </div>


                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('viaje_evaluacions.store') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="box-body">
                                @include('includes.messages')
                                <!-- Nav tabs -->

                                        <div class="row">

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="hidden" name="viaje_id" value="{{ $viaje->id ?? '' }}">
                                                    {{Form::label('periodo', 'Período')}}
                                                    {{Form::text('periodo', $viaje->periodo->nombre, ['class' => 'form-control','placeholder'=>'Período','disabled'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('cuil', 'CUIL')}}
                                                    {{Form::text('cuil', $viaje->investigador->persona->cuil, ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X','disabled'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('apellido', 'Apellido')}}
                                                    {{Form::text('apellido', $viaje->investigador->persona->apellido, ['class' => 'form-control','placeholder'=>'Apellido','disabled'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('nombre', 'Nombre')}}
                                                    {{Form::text('nombre', $viaje->investigador->persona->nombre, ['class' => 'form-control','placeholder'=>'Nombre','disabled'])}}
                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">

                                            <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                                <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Evaluadores internos</legend>

                                                <div class="form-group col-md-12">

                                                    <div class="table-responsive">
                                                        <table class="table" style="width: 50%">
                                                            <thead>

                                                            <th>Evaluador</th>

                                                            <th><a href="#" class="addRowInterno"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                            </thead>

                                                            <tbody id="cuerpoInternos">
                                                            @foreach ($viaje->evaluacions->where('interno', 1) as $interno)
                                                                <tr>

                                                                    <td>{{Form::hidden('internos_id[]',$interno->id)}}{{ Form::select('internos[]',$internos, $interno->user_id,['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px']) }}</td>

                                                                    <td><a href="#" class="btn btn-danger removeInterno"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                                </tr>
                                                            @endforeach
                                                            </tbody>




                                                        </table>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                <div class="row">

                                    <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                        <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Evaluadores externos</legend>

                                        <div class="form-group col-md-12">

                                            <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Evaluador</th>

                                                    <th><a href="#" class="addRowExterno"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                    </thead>

                                                    <tbody id="cuerpoExternos">
                                                    @foreach ($viaje->evaluacions->where('interno', 0) as $externo)

                                                        <tr>

                                                            <td>{{Form::hidden('externos_id[]',$externo->id)}}{{ Form::select('externos[]',$externos, $externo->user_id,['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px']) }}</td>

                                                            <td><a href="#" class="btn btn-danger removeExterno"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>




                                                </table>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href="{{ route('viaje_evaluacions.index') }}?viaje_id={{ $viaje->id }}" class="btn btn-warning">Volver</a>

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
    <script>
        $(document).ready(function () {
            $('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
            $('.js-example-basic-single').select2();

            $('.addRowInterno').on('click',function(e){
                e.preventDefault();
                addRowInterno();
            });
            function addRowInterno()
            {
                var tr='<tr>'+
                    '<td>'+'{{ Form::select('internos[]',$internos ?? [''=>''], '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px']) }}'+'</td>'+


                    '<td><a href="#" class="btn btn-danger removeInterno"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                    '</tr>';
                $('#cuerpoInternos').append(tr);
                $('.js-example-basic-single').select2();
            };

            $('body').on('click', '.removeInterno', function(e){

                e.preventDefault();
                var confirmDelete = confirm('¿Estás seguro?');

                if (confirmDelete) {
                    $(this).parent().parent().remove();
                }



            });

            $('.addRowExterno').on('click',function(e){
                e.preventDefault();
                addRowExterno();
            });
            function addRowExterno()
            {
                var tr='<tr>'+
                    '<td>'+'{{ Form::select('externos[]',$externos ?? [''=>''], '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px']) }}'+'</td>'+


                    '<td><a href="#" class="btn btn-danger removeExterno"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                    '</tr>';
                $('#cuerpoExternos').append(tr);
                $('.js-example-basic-single').select2();
            };

            $('body').on('click', '.removeExterno', function(e){

                e.preventDefault();
                var confirmDelete = confirm('¿Estás seguro?');

                if (confirmDelete) {
                    $(this).parent().parent().remove();
                }



            });

        });



    </script>

@endsection
