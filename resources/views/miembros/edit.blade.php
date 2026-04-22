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
                <i class="fa fa-user-friends" aria-hidden="true"></i>Miembro
                <small>Modificar Alta</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i>Home</a></li>
                <li><a href="{{ route('miembros.index') }}">Miembros</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">@if($unidad) {{ $unidad->denominacion }} {{ $unidad->sigla }}@endif</h3>
                        </div>


                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('miembros.update',$miembro->id) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
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
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="hidden" name="unidad_id" value="{{ $unidad->id ?? '' }}">
                                                    @php
                                                        $miembroTiposConfig = config('miembrosTipos');
                                                        $tipoUnidad = $unidad->tipo ?? 'Centro'; // o como se llame el campo

                                                        $miembroTipos = collect($miembroTiposConfig[$tipoUnidad] ?? [])
                                                            ->pluck('nombre', 'nombre')
                                                            ->toArray();
                                                    @endphp

                                                    {{Form::label('tipo', 'Tipo')}}
                                                    {{ Form::select('tipo',[''=>'']+$miembroTipos, $miembro->tipo,['class' => 'form-control', 'id' => 'tipo']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('horas', 'Horas')}}
                                                    {{Form::number('horas',  $miembro->horas, ['class' => 'form-control','placeholder'=>'Horas'])}}
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
                                            {{-- Campos para Investigador correspondiente --}}
                                            <div class="col-md-3" id="divLugarTrabajo" style="display:none;">
                                                <div class="form-group">
                                                    {{Form::label('lugar_trabajo', 'Lugar de trabajo permanente (Institución de referencia)')}}
                                                    {{Form::text('lugar_trabajo', $miembro->lugar_trabajo, ['class' => 'form-control', 'placeholder' => 'Lugar de trabajo'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="divHsLugar" style="display:none;">
                                                <div class="form-group">
                                                    {{Form::label('hs_lugar', 'Horas dedicadas')}}
                                                    {{Form::number('hs_lugar', $miembro->hs_lugar, ['class' => 'form-control', 'placeholder' => 'Horas'])}}
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">

                                            <div class="col-md-4">

                                                <div class="form-group">
                                                    {{Form::label('observaciones', 'Observaciones')}}
                                                    {{Form::textarea('observaciones', $miembro->observaciones, ['class' => 'form-control'])}}

                                                </div>
                                            </div>
                                        </div>

                                        <!--<div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="curriculum">Curriculum</label>
                                                    <input type="file" name="curriculum" class="form-control" placeholder="">

                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="actividades">Plan de trabajo</label>
                                                    <input type="file" name="actividades" class="form-control" placeholder="">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="importante" style="color: #a90062">IMPORTANTE: El CV deberá ser el generado (pdf/doc) por el sistema SIGEVA-UNLP (banco de datos de actividades de ciencia y técnica)</label>


                                                </div>
                                            </div>

                                        </div>-->
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

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('facultad', 'U. Académica')}}
                                                    {{Form::select('facultad_id',  $facultades,$miembro->facultad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'facultad_id'])}}

                                                </div>
                                            </div>




                                        </div>
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


                                                        </thead>

                                                        <tbody id="cuerpoCargos">
                                                        <tr>

                                                            <td>{{ Form::select('cargos[]',$cargos, $miembro->cargo_id,['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                            @php
                                                                $dedicaciones = config('dedicaciones');
                                                                unset($dedicaciones['Sin Dedicación']);
                                                            @endphp
                                                            <td>{{ Form::select('deddocs[]',['' => ''] + $dedicaciones, $miembro->deddoc,['class' => 'form-control', 'style' => 'width: 120px']) }}</td>

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


                                                        </thead>

                                                        <tbody id="cuerpoCarrerainvs">
                                                        <tr>

                                                            <td>{{ Form::select('carrerainvs[]',$carrerainvs, $miembro->carrerainv_id,['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                            <td>{{ Form::select('organismos[]',$organismos, $miembro->organismo_id,['class' => 'form-control', 'style' => 'width: 150px']) }}</td>




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


                                                        </thead>

                                                        <tbody id="cuerpoSicadis">
                                                        <tr>

                                                            <td>{{ Form::select('sicadis[]',$sicadis, $miembro->sicadi_id,['class' => 'form-control', 'style' => 'width: 120px']) }}</td>

                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="becario">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Beca</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>



                                                        <th>Beca</th>


                                                        </thead>

                                                        <tbody id="cuerpoBecas">
                                                        @php
                                                            $instituciones = config('becaEntidades');
                                                            unset($instituciones['Otra']);
                                                            $instituciones['CIN'] = 'CIN';
                                                        @endphp
                                                        <tr>


                                                            {{Form::text('becas[]', $miembro->beca, ['class' => 'form-control','placeholder'=>'Beca'])}}
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
                                        <a href="{{ route('miembros.index') }}?unidad_id={{ $unidad->id }}" class="btn btn-warning">Volver</a>

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
            $('select[name=country]').attr("disabled", "disabled");
            $('#cuerpoCategorias select[name="categorias[]"]').attr("disabled", "disabled");
            $('#cuerpoSicadis select[name="sicadis[]"]').attr("disabled", "disabled");

// Ocultar divMaterias por defecto
            @if($miembro->titulo_id)
                $('#divMaterias').hide();
            @endif
            //$('#divMaterias').hide();

            // Mostrar u ocultar divMaterias según la selección del select
            $(document).on('change', 'select[name="titulos[]"]', function() {
                if ($(this).val() !== '') {
                    $('#divMaterias').hide(); // Ocultar divMaterias si se selecciona algo en el select
                } else {
                    $('#divMaterias').show(); // Mostrar divMaterias si se selecciona vacío
                }
            });

            $('#tipo').on('change', function() {
                if ($(this).val() === 'Investigador correspondiente') {
                    $('#divLugarTrabajo, #divHsLugar').show();
                } else {
                    $('#divLugarTrabajo, #divHsLugar').hide();
                    $('input[name="lugar_trabajo"]').val('');
                    $('input[name="hs_lugar"]').val('');
                }
            });

            // Trigger on page load to show/hide based on existing value
            $('#tipo').trigger('change');

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
            if (!opciones) {
                return ['']; // Opción por defecto si opciones es null o undefined
            }
            if (opciones[institucionSeleccionada]) {
                return opciones[institucionSeleccionada];
            }
            return ['']; // Opción por defecto
        }
        // Disparar el evento change manualmente al cargar la página

    </script>

@endsection
