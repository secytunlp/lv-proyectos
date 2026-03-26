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
                <i class="fa fa-user-friends" aria-hidden="true"></i>Estado del miembro
                <small>Cambiar</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('miembros.index') }}">Miembros</a></li>
                <li><a href="{{ route('miembro_estados.index') }}">Estados</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">@if($miembro) - {{ $miembro->apellido }} {{ $miembro->nombre }} en la Unidad {{ $miembro->unidad->denominacion }} - {{ $miembro->unidad->sigla }}@endif</h3>
                        </div>


                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('miembro_estados.store') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="box-body">
                                @include('includes.messages')
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#datos_unidad" aria-controls="datos_unidad" role="tab" data-toggle="tab">Unidad</a></li>
                                    <li role="presentation"><a href="#datos_personales" aria-controls="datos_personales" role="tab" data-toggle="tab">Datos Personales</a></li>
                                    <li role="presentation"><a href="#universidad" aria-controls="universidad" role="tab" data-toggle="tab">Universidad</a></li>
                                    <li role="presentation"><a href="#investigacion" aria-controls="investigacion" role="tab" data-toggle="tab">Investigación</a></li>
                                    <li role="presentation"><a href="#categorizacion" aria-controls="categorizacion" role="tab" data-toggle="tab">Categorización</a></li>
                                    <li role="presentation"><a href="#becario" aria-controls="becario" role="tab" data-toggle="tab">Becas</a></li>
                                    <!-- Agrega más pestañas según sea necesario -->
                                </ul>
                                <div class="tab-content" style="margin: 1%;">
                                    <div role="tabpanel" class="tab-pane active" id="datos_unidad">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    {{Form::label('estado', 'Estado')}}
                                                    {{ Form::select('estado',[''=>'','Alta Creada'=>'Alta Creada','Alta Recibida'=>'Alta Recibida','Baja Creada'=>'Baja Creada','Baja Recibida'=>'Baja Recibida','Cambio Creado'=>'Cambio Creado','Cambio Recibido'=>'Cambio Recibido','Cambio Hs. Creado'=>'Cambio Hs. Creado','Cambio Hs. Recibido'=>'Cambio Hs. Recibido','Cambio Tipo Creado'=>'Cambio Tipo Creado','Cambio Tipo Recibido'=>'Cambio Tipo Recibido'], $miembro->estado,['class' => 'form-control']) }}

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="hidden" name="miembro_id" value="{{ $miembro->id ?? '' }}">
                                                    @php
                                                        $miembroTiposConfig = config('miembrosTipos');

                                                        $miembroTipos = collect($miembroTiposConfig)
                                                            ->flatten(1)
                                                            ->pluck('nombre','nombre')
                                                            ->unique()
                                                            ->toArray();

                                                    @endphp
                                                    {{Form::label('tipo', 'Tipo')}}
                                                    {{ Form::select('tipo',$miembroTipos, $miembro->tipo,['class' => 'form-control']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('horas', 'Horas')}}
                                                    {{Form::number('horas', $miembro->horas, ['class' => 'form-control','placeholder'=>'Horas'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('activo', 'Activo')}}
                                                    {{Form::checkbox('activo', 1,($miembro->activo)?true:false)}}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    {{Form::label('comentarios', 'Comentarios')}}
                                                    {{Form::textarea('comentarios', '', ['class' => 'form-control'])}}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="datos_personales">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('apellido', 'Apellido')}}
                                                    {{Form::text('apellido', $miembro->apellido, ['class' => 'form-control','placeholder'=>'Apellido'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('nombre', 'Nombre')}}
                                                    {{Form::text('nombre', $miembro->nombre, ['class' => 'form-control','placeholder'=>'Nombre'])}}
                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('cuil', 'CUIL')}}
                                                    {{Form::text('cuil', $miembro->cuil, ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('email', 'Email')}}
                                                    {{Form::email('email', $miembro->email, ['class' => 'form-control','placeholder'=>'Email'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('nacimiento', 'Nacimiento')}}
                                                    {{Form::date('nacimiento', ($miembro->nacimiento) ? date('Y-m-d', strtotime($miembro->nacimiento)) : '', ['class' => 'form-control'])}}
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="universidad">
                                        <div class="row">
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('estudiante', 'Estudiante')}}
                                                    {{Form::checkbox('estudiante', 1,($miembro->estudiante)?true:false)}}
                                                </div>
                                            </div>
                                        </div>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Cargo Docente</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Cargo</th>
                                                    <th>Dedicación</th>

                                                    <th>U. Académica</th>

                                                    <!--<th>Activo</th>
                                                    <th><a href="#" class="addRowCargo"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoCargos">
                                                    <tr>

                                                        <td>{{ Form::select('cargos[]',$cargos, $miembro->cargo_id,['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                        @php
                                                            $dedicaciones = config('dedicaciones');
                                                            unset($dedicaciones['Sin Dedicación']);
                                                        @endphp
                                                        <td>{{ Form::select('deddocs[]',['' => ''] + $dedicaciones, $miembro->deddoc,['class' => 'form-control', 'style' => 'width: 120px']) }}</td>

                                                        <td>{{ Form::select('facultads[]',$facultades, $miembro->facultad_id,['class' => 'form-control', 'style' => 'width: 300px']) }}</td>

                                                        <!--<td>{{Form::checkbox('activos[]', 1,true)}}</td>
                                                        <td><a href="#" class="btn btn-danger removeCargo"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                    </tr>

                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="investigacion">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Carrera de Investigación</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Cargo</th>
                                                    <th>Institución</th>


                                                    <!--<th>Actual</th>
                                                    <th><a href="#" class="addRowCarrerainv"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoCarrerainvs">
                                                    <tr>

                                                        <td>{{ Form::select('carrerainvs[]',$carrerainvs, $miembro->carrrera_id,['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                        <td>{{ Form::select('organismos[]',$organismos, $miembro->organismo_id,['class' => 'form-control', 'style' => 'width: 150px']) }}</td>



                                                        <!--<td>{{ Form::radio('actual', 1, true,['id' => 'actual_1']) }}</td>
                                                        <td><a href="#" class="btn btn-danger removeCarrerainv"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                    </tr>

                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="categorizacion">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Categoría SPU</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Categoría</th>
                                                    <!--<th>Año</th>
                                                    <th>Notificación</th>
                                                    <th>Universidad</th>
                                                    <th>Actual</th>
                                                    <th><a href="#" class="addRowCategoria"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoCategorias">
                                                    <tr>

                                                        <td>{{ Form::select('categorias[]',$categorias, $miembro->categoria_id,['class' => 'form-control', 'style' => 'width: 60px']) }}</td>

                                                    </tr>

                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Categoría SICADI</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Categoría</th>
                                                    <!--<th>Año</th>
                                                    <th>Notificación</th>

                                                  <th>Actual</th>
                                                    <th><a href="#" class="addRowSicadi"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoSicadis">
                                                    <tr>

                                                        <td>{{ Form::select('sicadis[]',$sicadis, $miembro->sicadi_id,['class' => 'form-control', 'style' => 'width: 120px']) }}</td>
                                                        <!--<td>{{ Form::select('sicadiyears[]',$years, '',['class' => 'form-control', 'style' => 'width: 60px']) }}</td>
                                                        <td>{{Form::date('sicadinotificacions[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>


                                                        <td>{{ Form::radio('sicadiactual', 1, true,['id' => 'sicadiactual_1']) }}</td>
                                                        <td><a href="#" class="btn btn-danger removeSicadi"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                    </tr>

                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="becario">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">


                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>



                                                    <th>Beca</th>
                                                    <!--<th>UNLP</th>
                                                    <th><a href="#" class="addRowBeca"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoBecas">
                                                    <tr>

                                                        <td>

                                                            {{Form::text('becas[]', $miembro->beca, ['class' => 'form-control'])}}
                                                        </td>

                                                    </tr>

                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>

                                    </div>
                                </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href="{{ route('miembro_estados.index') }}?miembro_id={{ $miembro->id }}" class="btn btn-warning">Volver</a>

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


// Ocultar divMaterias por defecto
            if ($('#cuerpoTitulo tr').length > 0) {
                $('#divMaterias').hide(); // Ocultar divMaterias si hay títulos presentes
            }

            // Mostrar u ocultar divMaterias según la selección del select
            $(document).on('change', 'select[name="titulos[]"]', function() {
                if ($(this).val() !== '') {
                    $('#divMaterias').hide(); // Ocultar divMaterias si se selecciona algo en el select
                } else {
                    $('#divMaterias').show(); // Mostrar divMaterias si se selecciona vacío
                }
            });


        });

        // Al cambiar cualquier select de instituciones
        $(document).on('change', 'select.institucion_select', function() {
            var rowIndex = $(this).closest('tr').index(); // Obtener el índice de la fila actual
            var institucionSeleccionada = $(this).val(); // Obtener la institución seleccionada

            // Obtener el select de becas en la misma fila
            var becasSelect = $('tbody#cuerpoBecas tr:eq(' + rowIndex + ') select.beca_select');
            var opciones = obtenerOpcionesBecaPorInstitucion(institucionSeleccionada); // Obtener opciones de beca

            // Limpiar el select de becas y agregar las nuevas opciones
            becasSelect.empty();
            opciones.forEach(function(opcion) {
                //console.log(opcion);
                becasSelect.append($('<option>', {
                    value: opcion,
                    text: opcion
                }));
            });
        });

        // Función para obtener opciones de beca según la institución seleccionada
        function obtenerOpcionesBecaPorInstitucion(institucionSeleccionada) {
            //console.log(institucionSeleccionada)
            var opciones = @json(config('becas'));
            // Verificar si opciones es null o undefined
            if (!opciones) {
                return ['']; // Opción por defecto si opciones es null o undefined
            }
            if (opciones[institucionSeleccionada]) {
                return opciones[institucionSeleccionada];
            }
            return ['']; // Opción por defecto
        }

    </script>

@endsection
