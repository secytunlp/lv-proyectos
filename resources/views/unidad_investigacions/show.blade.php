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

        .ck-editor__editable {
            min-height: 150px;
        }
    </style>

@endsection


@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-user-friends" aria-hidden="true"></i>Unidad de Investigación
                <small>Ver unidad de investigación</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i>Home</a></li>
                <li><a href="{{ route('unidad_investigacions.index') }}">Unidades de Investigación</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">

                        </div>


                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('unidad_investigacions.update',$unidad->id) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="box-body">
                                @include('includes.messages')
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#datos_unidad" aria-controls="datos_unidad" role="tab" data-toggle="tab">Unidad</a></li>
                                    <li role="presentation"><a href="#finalidad" aria-controls="finalidad" role="tab" data-toggle="tab">Finalidad</a></li>
                                    <li role="presentation"><a href="#antecedentes" aria-controls="antecedentes" role="tab" data-toggle="tab">Antecedentes</a></li>
                                    <li role="presentation"><a href="#funciones" aria-controls="funciones" role="tab" data-toggle="tab">Funciones</a></li>
                                    <li role="presentation"><a href="#dependencias" aria-controls="dependencias" role="tab" data-toggle="tab">Dependencias</a></li>

                                    <!-- Agrega más pestañas según sea necesario -->
                                </ul>
                                <div class="tab-content" style="margin: 1%;">
                                    <div role="tabpanel" class="tab-pane active" id="datos_unidad">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    {{Form::label('tipo', 'Tipo')}}
                                                    {{ Form::select('tipo',[''=>'']+config('unidadTipos'), $unidad->tipo,['class' => 'form-control','disabled']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('sigla', 'Sigla')}}
                                                    {{Form::text('sigla', $unidad->sigla, ['class' => 'form-control','placeholder'=>'Sigla','disabled'])}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">

                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('denominacion', 'Denominación')}}
                                                    {{Form::text('denominacion', $unidad->denominacion, ['class' => 'form-control','placeholder'=>'Denominación','disabled'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('especialidad', 'Especialidad')}}
                                                    {{Form::text('especialidad', $unidad->especialidad, ['class' => 'form-control','placeholder'=>'Especialidad','disabled'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    {{Form::label('fecha_disposicion', 'F. Disposición')}}
                                                    {{Form::date('fecha_disposicion', ($unidad->fecha_disposicion) ? date('Y-m-d', strtotime($unidad->fecha_disposicion)) : '', ['class' => 'form-control','disabled'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('disposicion', 'Nro. Disposición')}}
                                                    {{Form::text('disposicion', $unidad->disposicion, ['class' => 'form-control','placeholder'=>'Nro. Disposición','disabled'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5">

                                                <div class="form-group">
                                                    {{Form::label('observaciones', 'Observaciones')}}
                                                    {{Form::textarea('observaciones', $unidad->observaciones, ['class' => 'form-control','disabled'])}}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="finalidad">

                                        <div class="row">
                                            <div class="col-md-5">

                                                <div class="form-group">
                                                    {{ Form::label('objetivos', 'Objetivos') }}
                                                    {{ Form::textarea('objetivos', $unidad->objetivos, ['class' => 'form-control', 'id' => 'objetivos']) }}
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5">

                                                <div class="form-group">
                                                    {{ Form::label('lineas', 'Líneas de investigación') }}
                                                    {{ Form::textarea('lineas', $unidad->lineas, ['class' => 'form-control', 'id' => 'lineas']) }}
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5">

                                                <div class="form-group">
                                                    {{ Form::label('justificacion', 'Justificación de la propuesta') }}
                                                    {{ Form::textarea('justificacion', $unidad->justificacion, ['class' => 'form-control', 'id' => 'justificacion']) }}
                                                </div>

                                            </div>
                                        </div>


                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="antecedentes">
                                        <div class="row">
                                            <div class="col-md-5">

                                                <div class="form-group">
                                                    {{ Form::label('produccion', 'Producción científica en los últimos 4 años') }}
                                                    {{ Form::textarea('produccion', $unidad->produccion, ['class' => 'form-control', 'id' => 'produccion']) }}
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5">

                                                <div class="form-group">
                                                    {{ Form::label('proyectos', 'Proyectos acreditados en los últimos 3 años') }}
                                                    {{ Form::textarea('proyectos', $unidad->proyectos, ['class' => 'form-control', 'id' => 'proyectos']) }}
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5">

                                                <div class="form-group">
                                                    {{ Form::label('rrhh', 'Contribución a la formación de RR.HH.') }}
                                                    {{ Form::textarea('rrhh', $unidad->rrhh, ['class' => 'form-control', 'id' => 'rrhh']) }}
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="memorias">Memorias o balances</label>

                                                    @if(!empty($unidad->memorias))
                                                        <a href="{{ url($unidad->memorias) }}" target="_blank">Descargar Memorias</a>

                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="funciones">
                                        <div class="row">
                                            <div class="col-md-5">

                                                <div class="form-group">
                                                    {{ Form::label('funciones', 'Funciones') }}
                                                    {{ Form::textarea('funciones', $unidad->funciones, ['class' => 'form-control', 'id' => 'funciones-id']) }}
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5">

                                                <div class="form-group">
                                                    {{ Form::label('insfraestructura', 'Infraestructura (lugar físico, biblioteca, recurso económicos-financieros, convenios)') }}
                                                    {{ Form::textarea('insfraestructura', $unidad->insfraestructura, ['class' => 'form-control', 'id' => 'insfraestructura']) }}
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-5">

                                                <div class="form-group">
                                                    {{ Form::label('equipamiento', 'Equipamiento') }}
                                                    {{ Form::textarea('equipamiento', $unidad->equipamiento, ['class' => 'form-control', 'id' => 'equipamiento']) }}
                                                </div>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="reglamento">Reglamento interno</label>

                                                    @if(!empty($unidad->reglamento))
                                                        <a href="{{ url($unidad->reglamento) }}" target="_blank">Descargar Reglamento</a>

                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="dependencias">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Indique las dependencias académicas para la unidad</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>

                                                        <th>U. Académica</th>


                                                        </thead>

                                                        <tbody id="cuerpoFacultad">
                                                        @foreach($unidad->facultads as $index => $facultad)
                                                            @php

                                                                @endphp
                                                            <tr>
                                                                {{-- Usar el valor de ambito['institucion'] o el correspondiente --}}
                                                                <td>{{ Form::select('facultads[]',$facultades, $facultad->facultad_id ?? old("facultads.$index"), ['class' => 'form-control', 'style' => 'width: 600px','disabled']) }}</td>


                                                            </tr>
                                                        @endforeach

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>
                                        </fieldset>

                                    </div>

                                </div>
                                <div class="form-group">

                                    <a href="{{ route('unidad_investigacions.index') }}" class="btn btn-warning">Volver</a>

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

    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <!-- page script -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            ClassicEditor
                .create(document.querySelector('#objetivos'))
                .then(editor => {
                    editor.enableReadOnlyMode('readonly');
                })
                .catch(error => {
                    console.error(error);
                });
            ClassicEditor
                .create(document.querySelector('#lineas'))
                .then(editor => {
                    editor.enableReadOnlyMode('readonly');
                })
                .catch(error => {
                    console.error(error);
                });
            ClassicEditor
                .create(document.querySelector('#justificacion'))
                .then(editor => {
                    editor.enableReadOnlyMode('readonly');
                })
                .catch(error => {
                    console.error(error);
                });

            ClassicEditor
                .create(document.querySelector('#produccion'))
                .then(editor => {
                    editor.enableReadOnlyMode('readonly');
                })
                .catch(error => {
                    console.error(error);
                });
            ClassicEditor
                .create(document.querySelector('#proyectos'))
                .then(editor => {
                    editor.enableReadOnlyMode('readonly');
                })
                .catch(error => {
                    console.error(error);
                });
            ClassicEditor
                .create(document.querySelector('#rrhh'))
                .then(editor => {
                    editor.enableReadOnlyMode('readonly');
                })
                .catch(error => {
                    console.error(error);
                });
            ClassicEditor
                .create(document.querySelector('#funciones-id'))
                .then(editor => {
                    editor.enableReadOnlyMode('readonly');
                })
                .catch(error => {
                    console.error(error);
                });
            ClassicEditor
                .create(document.querySelector('#insfraestructura'))
                .then(editor => {
                    editor.enableReadOnlyMode('readonly');
                })
                .catch(error => {
                    console.error(error);
                });
            ClassicEditor
                .create(document.querySelector('#equipamiento'))
                .then(editor => {
                    editor.enableReadOnlyMode('readonly');
                })
                .catch(error => {
                    console.error(error);
                });




        });
        $(document).ready(function () {

            $('.js-example-basic-single').select2();







        });



    </script>

@endsection
