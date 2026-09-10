@extends('layouts.app')
@section('headSection')

    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">


@endsection


@section('content')
@php
    /*
     * Repoblado del formulario cuando el server rebota la validacion (store()).
     *
     * OJO con LaravelCollective: para un campo array como 'titulos[]', old() devuelve
     * el ARRAY completo y getSelectedValue() resuelve con in_array(), asi que todas
     * las filas terminarian marcando cualquiera de los valores enviados. Por eso las
     * filas repetibles se arman a mano con $selectFila()/$fechaFila() y no con Form::.
     * Los campos sueltos (apellido, email, etc.) si los repuebla Form:: solo.
     */
    $selectFila = function ($name, $list, $selected = '', $class = 'form-control', $style = '') {
        $html = '<select name="' . e($name) . '" class="' . e($class) . '"'
              . ($style !== '' ? ' style="' . e($style) . '"' : '') . '>';
        foreach ($list as $valor => $etiqueta) {
            $sel = ((string) $valor === (string) $selected) ? ' selected' : '';
            $html .= '<option value="' . e($valor) . '"' . $sel . '>' . e($etiqueta) . '</option>';
        }

        return $html . '</select>';
    };

    $fechaFila = function ($name, $valor = '', $style = 'width:150px;') {
        return '<input type="date" name="' . e($name) . '" class="form-control"'
             . ' style="' . e($style) . '" value="' . e($valor) . '">';
    };

    // Filas a dibujar: las que mando el usuario, o una fila vacia si no hubo POST.
    $filas = function ($campo) {
        $old = old($campo);

        return (is_array($old) && count($old)) ? $old : [''];
    };

    $institucionesBeca = ['' => '', 'ANPCyT' => 'ANPCyT', 'AGENCIA i+D+i' => 'AGENCIA i+D+i', 'CIC PBA' => 'CIC PBA', 'CIC' => 'CIC', 'CONICET' => 'CONICET', 'UNLP' => 'UNLP', 'CIN' => 'CIN', 'OTRA' => 'OTRA'];
    $tiposBeca = ['' => '', 'Beca inicial' => 'Beca inicial', 'Beca superior' => 'Beca superior', 'Beca de entrenamiento' => 'Beca de entrenamiento', 'Beca doctoral' => 'Beca doctoral', 'Beca posdoctoral' => 'Beca posdoctoral', 'Beca finalización del doctorado' => 'Beca finalización del doctorado', 'Beca maestría' => 'Beca maestría', 'Formación Superior' => 'Formación Superior', 'Iniciación' => 'Iniciación', 'TIPO I' => 'TIPO I', 'TIPO II' => 'TIPO II', 'TIPO A' => 'TIPO A', 'Tipo A - Maestría' => 'Tipo A - Maestría', 'Tipo A - Doctorado' => 'Tipo A - Doctorado', 'Beca Cofinanciada (UNLP-CIC)' => 'Beca Cofinanciada (UNLP-CIC)', 'Especial de Maestría' => 'Especial de Maestría', 'TIPO B' => 'TIPO B', 'TIPO B (DOCTORADO)' => 'TIPO B (DOCTORADO)', 'TIPO B (MAESTRÍA)' => 'TIPO B (MAESTRÍA)', 'BECA DE PERFECCIONAMIENTO' => 'BECA DE PERFECCIONAMIENTO', 'CONICET 2' => 'CONICET 2', 'RETENCION DE POSTGRADUADO' => 'RETENCION DE POSTGRADUADO', 'EVC' => 'EVC'];
@endphp
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-microscope" aria-hidden="true"></i>Investigador
                <small>Crear</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('investigadors.index') }}">Investigadores</a></li>
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
                        <form role="form" action="{{ route('investigadors.store') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="box-body">
                                @include('includes.messages')
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#datos_personales" aria-controls="datos_personales" role="tab" data-toggle="tab">Datos Personales</a></li>
                                    <li role="presentation"><a href="#universidad" aria-controls="universidad" role="tab" data-toggle="tab">Universidad</a></li>
                                    <li role="presentation"><a href="#investigacion" aria-controls="investigacion" role="tab" data-toggle="tab">Investigación</a></li>
                                    <li role="presentation"><a href="#categorizacion" aria-controls="categorizacion" role="tab" data-toggle="tab">Categorización</a></li>
                                    <li role="presentation"><a href="#becario" aria-controls="becario" role="tab" data-toggle="tab">Becas</a></li>
                                    <!-- Agrega más pestañas según sea necesario -->
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="datos_personales">

                                        <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{Form::label('apellido', 'Apellido')}}
                                                {{Form::text('apellido', $prefill['apellido'] ?? '', ['class' => 'form-control','placeholder'=>'Apellido'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{Form::label('nombre', 'Nombre')}}
                                                {{Form::text('nombre', $prefill['nombre'] ?? '', ['class' => 'form-control','placeholder'=>'Nombre'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('documento', 'Documento')}}
                                                {{Form::number('documento', $prefill['documento'] ?? '', ['class' => 'form-control','placeholder'=>'Documento'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('cuil', 'CUIL')}}
                                                {{Form::text('cuil', $prefill['documento'] ?? '', ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X'])}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                {{Form::label('email', 'Email')}}
                                                {{Form::email('email', '', ['class' => 'form-control','placeholder'=>'Email'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('genero', 'Género')}}
                                                {{ Form::select('genero',[''=>'Seleccionar...','F'=>'Mujer','MT'=>'Mujer-Trans','T'=>'Travesti','M'=>'Varón','VY'=>'Varón-Trans','NB'=>'No Binarie','O'=>'Otro','PN'=>'Prefiero no responder'], '',['class' => 'form-control','id'=>'genero']) }}

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('nacimiento', 'Nacimiento')}}
                                                {{Form::date('nacimiento', ($prefill['nacimiento'])?date('Y-m-d', strtotime($prefill['nacimiento'])):'', ['class' => 'form-control'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('fallecimiento', 'Fallecimiento')}}
                                                {{Form::date('fallecimiento', '', ['class' => 'form-control'])}}
                                            </div>
                                        </div>
                                    </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('telefono', 'Teléfono')}}
                                                    {{Form::text('telefono', '', ['class' => 'form-control','placeholder'=>'Teléfono'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('calle', 'Calle')}}
                                                    {{Form::text('calle', '', ['class' => 'form-control','placeholder'=>'Calle'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('nro', 'Número')}}
                                                    {{Form::text('nro', '', ['class' => 'form-control','placeholder'=>'Número'])}}

                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('piso', 'Piso')}}
                                                    {{Form::text('piso', '', ['class' => 'form-control','placeholder'=>'Piso'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('depto', 'Departamento')}}
                                                    {{Form::text('depto', '', ['class' => 'form-control','placeholder'=>'Departamento'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('localidad', 'Localidad')}}
                                                    {{Form::text('localidad', '', ['class' => 'form-control','placeholder'=>'Localidad'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('provincia', 'Provincia')}}
                                                    {{Form::select('provincia_id', ['' => 'Seleccionar...'] + $provincias->toArray(),'', ['class' => 'form-control js-example-basic-single','id'=>'provincia_id'])}}

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('cp', 'Código Postal')}}
                                                    {{Form::text('cp', '', ['class' => 'form-control','placeholder'=>'Código Postal'])}}
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">

                                                <div class="form-group">
                                                    <label for="foto">Foto</label>
                                                    <input type="file" name="foto" class="form-control" placeholder="">

                                                </div>
                                            </div>
                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    {{Form::label('observaciones', 'Observaciones')}}
                                                    {{Form::textarea('observaciones', '', ['class' => 'form-control'])}}

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="universidad">
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Títulos de Grado</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                            <table class="table" style="width: 50%">
                                                <thead>

                                                <th>Título</th>
                                                <th>Egreso</th>
                                                <th><a href="#" class="addRow"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                </thead>

                                                <tbody id="cuerpoTitulo">
                                                @php $oldEgresos = old('egresos', []); @endphp
                                                @foreach ($filas('titulos') as $i => $valTitulo)
                                                <tr>

                                                    <td>{!! $selectFila('titulos[]', $titulos, $valTitulo, 'form-control js-example-basic-single', 'width: 400px') !!}</td>
                                                    <td>{!! $fechaFila('egresos[]', $oldEgresos[$i] ?? '') !!}</td>

                                                    <td><a href="#" class="btn btn-danger remove"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                </tr>
                                                @endforeach
                                                </tbody>




                                            </table>
                                                </div>
                                        </div>

                                        </fieldset>

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;" id="divMaterias">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Estudiante</legend>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('carrera', 'Carrera')}}
                                                    {{Form::text('carrera', '', ['class' => 'form-control','placeholder'=>'Carrera'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('total', 'Total De Materias')}}
                                                    {{Form::number('total', '', ['class' => 'form-control','placeholder'=>'Total De Materias'])}}

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('materias', 'Aprobadas')}}
                                                    {{Form::number('materias', '', ['class' => 'form-control','placeholder'=>'Aprobadas'])}}
                                                </div>
                                            </div>

                                        </div>
                                        </fieldset>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Títulos de Posgrado</legend>

                                            <div class="form-group col-md-12">
                                                <div class="table-responsive">

                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Título</th>
                                                    <th>Egreso</th>
                                                    <th><a href="#" class="addRowPost"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                    </thead>

                                                    <tbody id="cuerpoPosgrado">
                                                    @php $oldEgresoposts = old('egresoposts', []); @endphp
                                                    @foreach ($filas('tituloposts') as $i => $valTitulopost)
                                                    <tr>

                                                        <td>{!! $selectFila('tituloposts[]', $tituloposts, $valTitulopost, 'form-control js-example-basic-single', 'width: 400px') !!}</td>
                                                        <td>{!! $fechaFila('egresoposts[]', $oldEgresoposts[$i] ?? '') !!}</td>

                                                        <td><a href="#" class="btn btn-danger removePost"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Cargos Docentes</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Cargo</th>
                                                    <th>Dedicación</th>
                                                    <th>Ingreso</th>
                                                    <th>U. Académica</th>
                                                    <th>Universidad</th>
                                                    <th>Activo</th>
                                                    <th><a href="#" class="addRowCargo"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                    </thead>

                                                    <tbody id="cuerpoCargos">
                                                    @php
                                                        $dedicaciones = config('dedicaciones');
                                                        unset($dedicaciones['Sin Dedicación']);
                                                        $oldDeddocs = old('deddocs', []);
                                                        $oldIngresos = old('ingresos', []);
                                                        $oldFacultads = old('facultads', []);
                                                        $oldUniversidads = old('universidads', []);
                                                        $oldActivos = old('activos', []);
                                                        $huboPostCargos = is_array(old('cargos'));
                                                    @endphp
                                                    @foreach ($filas('cargos') as $i => $valCargo)
                                                    <tr>

                                                        <td>{!! $selectFila('cargos[]', $cargos, $valCargo, 'form-control', 'width: 200px') !!}</td>
                                                        <td>{!! $selectFila('deddocs[]', ['' => ''] + $dedicaciones, $oldDeddocs[$i] ?? '', 'form-control', 'width: 120px') !!}</td>
                                                        <td>{!! $fechaFila('ingresos[]', $oldIngresos[$i] ?? '') !!}</td>
                                                        <td>{!! $selectFila('facultads[]', $facultades, $oldFacultads[$i] ?? '', 'form-control', 'width: 300px') !!}</td>
                                                        <td>{!! $selectFila('universidads[]', $universidades, $oldUniversidads[$i] ?? '', 'form-control js-example-basic-single', 'width: 300px') !!}</td>
                                                        <td><input type="checkbox" name="activos[]" value="1" {{ $huboPostCargos ? (isset($oldActivos[$i]) ? 'checked' : '') : 'checked' }}></td>
                                                        <td><a href="#" class="btn btn-danger removeCargo"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="investigacion">
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    {{Form::label('unidad', 'Lugar de Trabajo')}}
                                                    {{Form::select('unidad_id',  $unidads,'', ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidad_id'])}}

                                                </div>
                                            </div>


                                        </div>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Carrera de Investigación</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Cargo</th>
                                                    <th>Institución</th>
                                                    <th>Ingreso</th>

                                                    <th>Actual</th>
                                                    <th><a href="#" class="addRowCarrerainv"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                    </thead>

                                                    <tbody id="cuerpoCarrerainvs">
                                                    @php
                                                        $oldOrganismos = old('organismos', []);
                                                        $oldCarrIngresos = old('carringresos', []);
                                                    @endphp
                                                    @foreach ($filas('carrerainvs') as $i => $valCarrera)
                                                    <tr>

                                                        <td>{!! $selectFila('carrerainvs[]', $carrerainvs, $valCarrera, 'form-control', 'width: 200px') !!}</td>
                                                        <td>{!! $selectFila('organismos[]', $organismos, $oldOrganismos[$i] ?? '', 'form-control', 'width: 150px') !!}</td>
                                                        <td>{!! $fechaFila('carringresos[]', $oldCarrIngresos[$i] ?? '') !!}</td>


                                                        <td>{{ Form::radio('actual', $i + 1, $i === 0, ['id' => 'actual_' . ($i + 1)]) }}</td>
                                                        <td><a href="#" class="btn btn-danger removeCarrerainv"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="3" style="text-align: right; font-style: italic;">Ninguna (sin carrera actual)</td>
                                                        <td>{{ Form::radio('actual', 0, false, ['id' => 'actual_0']) }}</td>
                                                        <td></td>
                                                    </tr>
                                                    </tfoot>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="categorizacion">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Categorías SPU</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Categoría</th>
                                                    <th>Año</th>
                                                    <th>Notificación</th>
                                                    <th>Universidad</th>
                                                    <th>Actual</th>
                                                    <th><a href="#" class="addRowCategoria"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                    </thead>

                                                    <tbody id="cuerpoCategorias">
                                                    @php
                                                        $oldCatYears = old('catyears', []);
                                                        $oldCatNotifs = old('catnotificacions', []);
                                                        $oldCatUnivs = old('catuniversidads', []);
                                                    @endphp
                                                    @foreach ($filas('categorias') as $i => $valCategoria)
                                                    <tr>

                                                        <td>{!! $selectFila('categorias[]', $categorias, $valCategoria, 'form-control', 'width: 60px') !!}</td>
                                                        <td>{!! $selectFila('catyears[]', ['' => ''] + $years, $oldCatYears[$i] ?? '', 'form-control', 'width: 80px') !!}</td>
                                                        <td>{!! $fechaFila('catnotificacions[]', $oldCatNotifs[$i] ?? '') !!}</td>
                                                        <td>{!! $selectFila('catuniversidads[]', $universidades, $oldCatUnivs[$i] ?? '', 'form-control js-example-basic-single', 'width: 300px') !!}</td>

                                                        <td>{{ Form::radio('catactual', $i + 1, $i === 0, ['id' => 'catactual_' . ($i + 1)]) }}</td>
                                                        <td><a href="#" class="btn btn-danger removeCategoria"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="4" style="text-align: right; font-style: italic;">Ninguna (sin categoría actual)</td>
                                                        <td>{{ Form::radio('catactual', 0, false, ['id' => 'catactual_0']) }}</td>
                                                        <td></td>
                                                    </tr>
                                                    </tfoot>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Categorías SICADI</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Categoría</th>
                                                    <th>Año</th>
                                                    <th>Notificación</th>

                                                    <th>Actual</th>
                                                    <th><a href="#" class="addRowSicadi"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                    </thead>

                                                    <tbody id="cuerpoSicadis">
                                                    @php
                                                        $oldSicadiYears = old('sicadiyears', []);
                                                        $oldSicadiNotifs = old('sicadinotificacions', []);
                                                    @endphp
                                                    @foreach ($filas('sicadis') as $i => $valSicadi)
                                                    <tr>

                                                        <td>{!! $selectFila('sicadis[]', $sicadis, $valSicadi, 'form-control', 'width: 120px') !!}</td>
                                                        <td>{!! $selectFila('sicadiyears[]', ['' => ''] + $years, $oldSicadiYears[$i] ?? '', 'form-control', 'width: 80px') !!}</td>
                                                        <td>{!! $fechaFila('sicadinotificacions[]', $oldSicadiNotifs[$i] ?? '') !!}</td>


                                                        <td>{{ Form::radio('sicadiactual', $i + 1, $i === 0, ['id' => 'sicadiactual_' . ($i + 1)]) }}</td>
                                                        <td><a href="#" class="btn btn-danger removeSicadi"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="3" style="text-align: right; font-style: italic;">Ninguna (sin categoría actual)</td>
                                                        <td>{{ Form::radio('sicadiactual', 0, false, ['id' => 'sicadiactual_0']) }}</td>
                                                        <td></td>
                                                    </tr>
                                                    </tfoot>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="becario">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Becas</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>


                                                    <th>Institución</th>
                                                    <th>Beca</th>
                                                    <th>Desde</th>
                                                    <th>Hasta</th>
                                                    <th>UNLP</th>
                                                    <th><a href="#" class="addRowBeca"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                    </thead>

                                                    <tbody id="cuerpoBecas">
                                                    @php
                                                        $oldBecas = old('becas', []);
                                                        $oldBecaDesdes = old('becadesdes', []);
                                                        $oldBecaHastas = old('becahastas', []);
                                                        $oldBecaUnlps = old('becaunlps', []);
                                                    @endphp
                                                    @foreach ($filas('institucions') as $i => $valInstitucion)
                                                    <tr>

                                                        <td>{!! $selectFila('institucions[]', $institucionesBeca, $valInstitucion, 'form-control institucion_select', 'width: 150px') !!}</td>
                                                        <td>{!! $selectFila('becas[]', $tiposBeca, $oldBecas[$i] ?? '', 'form-control beca_select', 'width: 150px') !!}</td>

                                                        <td>{!! $fechaFila('becadesdes[]', $oldBecaDesdes[$i] ?? '') !!}</td>

                                                        <td>{!! $fechaFila('becahastas[]', $oldBecaHastas[$i] ?? '') !!}</td>
                                                        <td><input type="checkbox" name="becaunlps[]" value="1" {{ isset($oldBecaUnlps[$i]) ? 'checked' : '' }}></td>
                                                        <td><a href="#" class="btn btn-danger removeBeca"><i class="glyphicon glyphicon-remove"></i></a></td>
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
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href='{{ route('investigadors.index') }}' class="btn btn-warning">Volver</a>
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

    <script src="{{ asset('dist/js/confirm-exit.js') }}"></script>
    <!-- page script -->
    <script>
        // Plantillas para las filas nuevas. Se generan sin old() a proposito: las filas
        // que vuelven de un POST rebotado ya las dibuja Blade arriba.
        var tplTitulo        = @json($selectFila('titulos[]', $titulos, '', 'form-control js-example-basic-single', 'width: 400px'));
        var tplEgreso        = @json($fechaFila('egresos[]'));
        var tplTitulopost    = @json($selectFila('tituloposts[]', $tituloposts, '', 'form-control js-example-basic-single', 'width: 400px'));
        var tplEgresopost    = @json($fechaFila('egresoposts[]'));
        var tplCargo         = @json($selectFila('cargos[]', $cargos, '', 'form-control', 'width: 200px'));
        var tplDeddoc        = @json($selectFila('deddocs[]', ['' => ''] + $dedicaciones, '', 'form-control', 'width: 120px'));
        var tplIngreso       = @json($fechaFila('ingresos[]'));
        var tplFacultad      = @json($selectFila('facultads[]', $facultades, '', 'form-control', 'width: 300px'));
        var tplUniversidad   = @json($selectFila('universidads[]', $universidades, '', 'form-control js-example-basic-single', 'width: 300px'));
        var tplCarrerainv    = @json($selectFila('carrerainvs[]', $carrerainvs, '', 'form-control', 'width: 200px'));
        var tplOrganismo     = @json($selectFila('organismos[]', $organismos, '', 'form-control', 'width: 150px'));
        var tplCarringreso   = @json($fechaFila('carringresos[]'));
        var tplCategoria     = @json($selectFila('categorias[]', $categorias, '', 'form-control', 'width: 60px'));
        var tplCatyear       = @json($selectFila('catyears[]', ['' => ''] + $years, '', 'form-control', 'width: 60px'));
        var tplCatnotif      = @json($fechaFila('catnotificacions[]'));
        var tplCatuniversidad= @json($selectFila('catuniversidads[]', $universidades, '', 'form-control js-example-basic-single', 'width: 300px'));
        var tplSicadi        = @json($selectFila('sicadis[]', $sicadis, '', 'form-control', 'width: 120px'));
        var tplSicadiyear    = @json($selectFila('sicadiyears[]', ['' => ''] + $years, '', 'form-control', 'width: 60px'));
        var tplSicadinotif   = @json($fechaFila('sicadinotificacions[]'));
        var tplInstitucion   = @json($selectFila('institucions[]', $institucionesBeca, '', 'form-control institucion_select', 'width: 150px'));
        var tplBeca          = @json($selectFila('becas[]', $tiposBeca, '', 'form-control beca_select', 'width: 150px'));
        var tplBecadesde     = @json($fechaFila('becadesdes[]'));
        var tplBecahasta     = @json($fechaFila('becahastas[]'));

        $(document).ready(function () {
            $('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
            $('.js-example-basic-single').select2();


// Ocultar divMaterias por defecto
            //$('#divMaterias').hide();

            // Mostrar u ocultar divMaterias según la selección del select
            $(document).on('change', 'select[name="titulos[]"]', function() {
                if ($(this).val() !== '') {
                    $('#divMaterias').hide(); // Ocultar divMaterias si se selecciona algo en el select
                } else {
                    $('#divMaterias').show(); // Mostrar divMaterias si se selecciona vacío
                }
            });

            // Marcar la primera fila por defecto SOLO si no venimos de un POST rebotado.
            // Si hay old(), el radio correcto ya viene marcado desde el server y no hay
            // que pisarlo.
            @if (old('actual') === null)
            $('input[name="actual"]').prop('checked', false);
            $('#actual_1').prop('checked', true);
            @endif

            @if (old('catactual') === null)
            $('input[name="catactual"]').prop('checked', false);
            $('#catactual_1').prop('checked', true);
            @endif

            @if (old('sicadiactual') === null)
            $('input[name="sicadiactual"]').prop('checked', false);
            $('#sicadiactual_1').prop('checked', true);
            @endif



        });
        $('.addRow').on('click',function(e){
            e.preventDefault();
            addRow();
        });
        function addRow()
        {
            var tr='<tr>'+
                '<td>'+tplTitulo+'</td>'+
                '<td>'+tplEgreso+'</td>'+

                '<td><a href="#" class="btn btn-danger remove"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoTitulo').append(tr);
            $('.js-example-basic-single').select2();
        };

        $('body').on('click', '.remove', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
            }
            if ($('#cuerpoTitulo tr').length === 0) {
                $('#divMaterias').show();
            }


        });
        $('.addRowPost').on('click',function(e){
            e.preventDefault();
            addRowPost();
        });
        function addRowPost()
        {
            var tr='<tr>'+
                '<td>'+tplTitulopost+'</td>'+
                '<td>'+tplEgresopost+'</td>'+

                '<td><a href="#" class="btn btn-danger removePost"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoPosgrado').append(tr);
            $('.js-example-basic-single').select2();
        };

        $('body').on('click', '.removePost', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
            }


        });

        $('.addRowCargo').on('click',function(e){
            e.preventDefault();
            addRowCargo();
        });
        function addRowCargo()
        {
            var tr='<tr>'+
                '<td>'+tplCargo+'</td>'+
                '<td>'+tplDeddoc+'</td>'+
                '<td>'+tplIngreso+'</td>'+
                '<td>'+tplFacultad+'</td>'+
                '<td>'+tplUniversidad+'</td>'+
                '<td><input type="checkbox" name="activos[]" value="1" checked></td>'+
                '<td><a href="#" class="btn btn-danger removeCargo"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoCargos').append(tr);
            $('.js-example-basic-single').select2();

        };

        $('body').on('click', '.removeCargo', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
            }


        });

        $('.addRowCarrerainv').on('click',function(e){
            e.preventDefault();
            addRowCarrerainv();
        });
        function addRowCarrerainv()
        {
            var tr='<tr>'+
                '<td>'+tplCarrerainv+'</td>'+
                '<td>'+tplOrganismo+'</td>'+
                '<td>'+tplCarringreso+'</td>'+


                '<td><input type="radio" name="actual" id="actual_' + ($("#cuerpoCarrerainvs input[name=actual]").length + 1) + '" value="' + ($("#cuerpoCarrerainvs input[name=actual]").length + 1) + '"></td>' +


                '<td><a href="#" class="btn btn-danger removeCarrerainv"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoCarrerainvs').append(tr);



        };

        $('body').on('click', '.removeCarrerainv', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
            }


        });

        $('.addRowCategoria').on('click',function(e){
            e.preventDefault();
            addRowCategoria();
        });
        function addRowCategoria()
        {
            var tr='<tr>'+
                '<td>'+tplCategoria+'</td>'+
                '<td>'+tplCatyear+'</td>'+
                '<td>'+tplCatnotif+'</td>'+
                '<td>'+tplCatuniversidad+'</td>'+



                '<td><input type="radio" name="catactual" id="catactual_' + ($("#cuerpoCategorias input[name=catactual]").length + 1) + '" value="' + ($("#cuerpoCategorias input[name=catactual]").length + 1) + '"></td>' +


                '<td><a href="#" class="btn btn-danger removeCategoria"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoCategorias').append(tr);
            $('.js-example-basic-single').select2();


        };

        $('body').on('click', '.removeCategoria', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
                // Renumera los radios para que el value siga coincidiendo con el orden de las filas
                $('#cuerpoCategorias tr').each(function(i){
                    $(this).find('input[type="radio"][name="catactual"]')
                        .attr('id', 'catactual_' + (i + 1))
                        .attr('value', i + 1);
                });
            }


        });

        $('.addRowSicadi').on('click',function(e){
            e.preventDefault();
            addRowSicadi();
        });
        function addRowSicadi()
        {
            var tr='<tr>'+
                '<td>'+tplSicadi+'</td>'+
                '<td>'+tplSicadiyear+'</td>'+
                '<td>'+tplSicadinotif+'</td>'+




                '<td><input type="radio" name="sicadiactual" id="sicadiactual_' + ($("#cuerpoSicadis input[name=sicadiactual]").length + 1) + '" value="' + ($("#cuerpoSicadis input[name=sicadiactual]").length + 1) + '"></td>' +


                '<td><a href="#" class="btn btn-danger removeSicadi"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoSicadis').append(tr);



        };

        $('body').on('click', '.removeSicadi', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
                // Renumera los radios para que el value siga coincidiendo con el orden de las filas
                $('#cuerpoSicadis tr').each(function(i){
                    $(this).find('input[type="radio"][name="sicadiactual"]')
                        .attr('id', 'sicadiactual_' + (i + 1))
                        .attr('value', i + 1);
                });
            }


        });

        $('.addRowBeca').on('click',function(e){
            e.preventDefault();
            addRowBeca();
        });
        function addRowBeca()
        {
            var tr='<tr>'+
                '<td>'+tplInstitucion+'</td>'+
                '<td>'+tplBeca+'</td>'+
                '<td>'+tplBecadesde+'</td>'+
                '<td>'+tplBecahasta+'</td>'+
                '<td><input type="checkbox" name="becaunlps[]" value="1"></td>'+
                '<td><a href="#" class="btn btn-danger removeBeca"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoBecas').append(tr);



        };

        $('body').on('click', '.removeBeca', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
            }


        });


        // Al cambiar cualquier select de instituciones
        /*$(document).on('change', 'select.institucion_select', function() {
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
            console.log(institucionSeleccionada)
            var opciones = @json(config('becas'));
            if (!opciones) {
                return ['']; // Opción por defecto si opciones es null o undefined
            }
            if (opciones[institucionSeleccionada]) {
                return opciones[institucionSeleccionada];
            }
            return ['']; // Opción por defecto
        }*/

        $(document).ready(function () {

            let cargo = "{{ request('cargo') }}";
            let facultad = "{{ request('facultad') }}";
            let deddoc = "{{ request('deddoc') }}";
            let ingreso = "{{ request('ingreso') }}";

            if (cargo) {

                let tr = `
        <tr>
            <td>
                <select name="cargos[]" class="form-control" style="width: 200px">
                    @foreach($cargos as $id => $nombre)
                <option value="{{ $id }}" ${cargo == "{{ $id }}" ? 'selected' : ''}>
                            {{ $nombre }}
                </option>
@endforeach
                </select>
            </td>

            <td>
                <select name="deddocs[]" class="form-control" style="width: 120px">
                    <option value="">--</option>
                    <option value="Exclusiva" ${deddoc == 'Exclusiva' ? 'selected' : ''}>Exclusiva</option>
                    <option value="Semi Exclusiva" ${deddoc == 'Semi Exclusiva' ? 'selected' : ''}>Semi Exclusiva</option>
                    <option value="Simple" ${deddoc == 'Simple' ? 'selected' : ''}>Simple</option>
                </select>
            </td>

            <td><input type="date" name="ingresos[]" class="form-control" value="${ingreso ?? ''}" style="width: 150px"></td>

            <td>
                <select name="facultads[]" class="form-control" style="width: 300px">
                    @foreach($facultades as $id => $nombre)
                <option value="{{ $id }}" ${facultad == "{{ $id }}" ? 'selected' : ''}>
                            {{ $nombre }}
                </option>
@endforeach
                </select>
            </td>

            <td>
                <select name="universidads[]" class="form-control" style="width: 300px">
                    @foreach($universidades as $id => $nombre)
                <option value="{{ $id }}" {{ $id == 11 ? 'selected' : '' }}>{{ $nombre }}</option>
                    @endforeach
                </select>
            </td>

            <td><input type="checkbox" name="activos[]" value="1" checked></td>
            <td><a href="#" class="btn btn-danger removeCargo"><i class="glyphicon glyphicon-remove"></i></a></td>
        </tr>
        `;

                $('#cuerpoCargos').html(tr);
            }

        });


    </script>

@endsection
