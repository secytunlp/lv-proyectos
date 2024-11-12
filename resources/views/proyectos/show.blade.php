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
                <i class="fa fa-user-friends" aria-hidden="true"></i>Proyecto
                <small>Ver</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('proyectos.index') }}">Proyectos</a></li>
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
                        <form role="form" action="{{ route('integrantes.update',$proyecto->id) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="box-body">
                                @include('includes.messages')
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#datos_proyecto" aria-controls="datos_proyecto" role="tab" data-toggle="tab">Proyecto</a></li>
                                    <li role="presentation"><a href="#investigacion" aria-controls="investigacion" role="tab" data-toggle="tab">Investigación</a></li>

                                    <li role="presentation"><a href="#resumen" aria-controls="resumen" role="tab" data-toggle="tab">Resumen</a></li>
                                    <!-- Agrega más pestañas según sea necesario -->
                                </ul>
                                <div class="tab-content" style="margin: 1%;">
                                    <div role="tabpanel" class="tab-pane active" id="datos_proyecto">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    {{Form::label('tipo', 'Tipo')}}
                                                    {{Form::text('tipo', $proyecto->tipo, ['class' => 'form-control','disabled'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    {{Form::label('codigo', 'Código')}}
                                                    {{Form::text('codigo', $proyecto->codigo, ['class' => 'form-control','disabled'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('sigeva', 'Código SIGEVA')}}
                                                    {{Form::text('sigeva', $proyecto->sigeva, ['class' => 'form-control','disabled'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    {{Form::label('titulo', 'Título')}}
                                                    {{Form::textarea('titulo', $proyecto->titulo, ['class' => 'form-control','disabled', 'rows' => 3])}}

                                                </div>
                                            </div>



                                        </div>
                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('director', 'Director')}}
                                                    {{Form::text('director', $director->director_apellido, ['class' => 'form-control','disabled'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-2">

                                                <div class="form-group">
                                                    {{Form::label('inicio', 'Fecha de inicio')}}
                                                    {{Form::date('inicio', ($proyecto->inicio)?date('Y-m-d', strtotime($proyecto->inicio)):'', ['class' => 'form-control','disabled'])}}

                                                </div>
                                            </div>
                                            <div class="col-md-2">

                                                <div class="form-group">
                                                    {{Form::label('fin', 'Fecha de fin')}}
                                                    {{Form::date('fin', ($proyecto->fin)?date('Y-m-d', strtotime($proyecto->fin)):'', ['class' => 'form-control','disabled'])}}

                                                </div>
                                            </div>

                                            <div class="col-md-2">

                                                <div class="form-group">
                                                    {{Form::label('estado', 'Estado')}}
                                                    {{Form::text('estado', $proyecto->estado, ['class' => 'form-control','disabled'])}}

                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="investigacion">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('facultad', 'U. Académica')}}
                                                    {{Form::select('facultad_id',  $facultades,$proyecto->facultad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'facultad_id','disabled'])}}

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    {{Form::label('unidad', 'Unidad de investigación')}}
                                                    {{Form::select('unidad_id',  $unidads,$proyecto->unidad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidad_id','disabled'])}}

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    @php
                                                    $especialidad = $proyecto->disciplina()->nombre;
                                                    if ($proyecto->especialidad()){
                                                        $especialidad .= ' - '.$proyecto->especialidad()->nombre;
                                                    }
                                                    @endphp
                                                    {{Form::label('especialidad', 'Especialidad')}}

                                                    {{Form::text('especialidad', $especialidad, ['class' => 'form-control','disabled'])}}
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('tipoinv', 'Tipo de investigación')}}
                                                    {{Form::text('tipoinv', $proyecto->investigacion, ['class' => 'form-control','disabled'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">

                                                    {{Form::label('campo', 'Campo de aplicación')}}

                                                    {{Form::select('campo_id',  $campos,$proyecto->campo_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'campo_id','disabled'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('linea', 'Línea de investigación')}}
                                                    {{Form::text('linea', $proyecto->linea, ['class' => 'form-control','disabled'])}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="resumen">
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    {{Form::label('abstract', 'Resumen')}}
                                                    {{Form::textarea('abstract', $proyecto->resumen, ['class' => 'form-control','disabled'])}}

                                                </div>
                                            </div>



                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    {{Form::label('claves', 'Palabras claves')}}

                                                    {{Form::text('claves', $proyecto->clave1.' - '.$proyecto->clave2.' - '.$proyecto->clave3.' - '.$proyecto->clave4.' - '.$proyecto->clave5.' - '.$proyecto->clave6, ['class' => 'form-control','disabled'])}}
                                                </div>
                                            </div>



                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    {{Form::label('keys', 'Palabras claves en inglés')}}

                                                    {{Form::text('keys', $proyecto->key1.' - '.$proyecto->key2.' - '.$proyecto->key3.' - '.$proyecto->key4.' - '.$proyecto->key5.' - '.$proyecto->key6, ['class' => 'form-control','disabled'])}}
                                                </div>
                                            </div>



                                        </div>

                                    </div>
                                </div>
                                    <div class="form-group">
                                        <!--<button type="submit" class="btn btn-primary">Guardar</button>-->
                                        <a href="{{ route('proyectos.index') }}" class="btn btn-warning">Volver</a>

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
    <!-- page script -->




@endsection
