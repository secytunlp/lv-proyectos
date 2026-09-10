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
     * Repoblado del formulario cuando el server rebota la validacion (update()).
     *
     * OJO con LaravelCollective: para un campo array como 'titulos[]', old() devuelve
     * el ARRAY completo y getSelectedValue() resuelve con in_array(), asi que todas
     * las filas terminarian marcando cualquiera de los valores enviados. Por eso las
     * filas repetibles se arman a mano con $selectFila()/$fechaFila() y no con Form::.
     * Los campos sueltos (apellido, email, etc.) si los repuebla Form:: solo.
     *
     * Cada tabla se dibuja desde old() si venimos de un POST rebotado, y desde el
     * modelo si es una carga normal de la pantalla.
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

    $fechaIso = function ($valor) {
        return $valor ? date('Y-m-d', strtotime($valor)) : '';
    };

    $dedicaciones = config('dedicaciones');
    unset($dedicaciones['Sin Dedicación']);

    $institucionesBeca = ['' => '', 'ANPCyT' => 'ANPCyT', 'AGENCIA i+D+i' => 'AGENCIA i+D+i', 'CIC PBA' => 'CIC PBA', 'CIC' => 'CIC', 'CONICET' => 'CONICET', 'UNLP' => 'UNLP', 'CIN' => 'CIN', 'OTRA' => 'OTRA'];
    $tiposBeca = ['' => '', 'Beca inicial' => 'Beca inicial', 'Beca superior' => 'Beca superior', 'Beca de entrenamiento' => 'Beca de entrenamiento', 'Beca doctoral' => 'Beca doctoral', 'Beca posdoctoral' => 'Beca posdoctoral', 'Beca finalización del doctorado' => 'Beca finalización del doctorado', 'Beca maestría' => 'Beca maestría', 'Formación Superior' => 'Formación Superior', 'Iniciación' => 'Iniciación', 'TIPO I' => 'TIPO I', 'TIPO II' => 'TIPO II', 'TIPO A' => 'TIPO A', 'Tipo A - Maestría' => 'Tipo A - Maestría', 'Tipo A - Doctorado' => 'Tipo A - Doctorado', 'Beca Cofinanciada (UNLP-CIC)' => 'Beca Cofinanciada (UNLP-CIC)', 'Especial de Maestría' => 'Especial de Maestría', 'TIPO B' => 'TIPO B', 'TIPO B (DOCTORADO)' => 'TIPO B (DOCTORADO)', 'TIPO B (MAESTRÍA)' => 'TIPO B (MAESTRÍA)', 'BECA DE PERFECCIONAMIENTO' => 'BECA DE PERFECCIONAMIENTO', 'CONICET 2' => 'CONICET 2', 'RETENCION DE POSTGRADUADO' => 'RETENCION DE POSTGRADUADO', 'EVC' => 'EVC'];

    // --- Titulos de grado ---
    $filasTitulos = [];
    if (is_array(old('titulos'))) {
        foreach (old('titulos') as $i => $v) {
            $filasTitulos[] = ['titulo' => $v, 'egreso' => old('egresos')[$i] ?? ''];
        }
    } else {
        foreach ($investigador->titulos as $t) {
            $filasTitulos[] = ['titulo' => $t->pivot->titulo_id, 'egreso' => $fechaIso($t->pivot->egreso)];
        }
    }

    // --- Titulos de posgrado ---
    $filasPosgrados = [];
    if (is_array(old('tituloposts'))) {
        foreach (old('tituloposts') as $i => $v) {
            $filasPosgrados[] = ['titulo' => $v, 'egreso' => old('egresoposts')[$i] ?? ''];
        }
    } else {
        foreach ($investigador->tituloposts as $t) {
            $filasPosgrados[] = ['titulo' => $t->pivot->titulo_id, 'egreso' => $fechaIso($t->pivot->egreso)];
        }
    }

    // --- Cargos docentes ---
    $filasCargos = [];
    if (is_array(old('cargos'))) {
        foreach (old('cargos') as $i => $v) {
            $filasCargos[] = [
                'cargo' => $v,
                'deddoc' => old('deddocs')[$i] ?? '',
                'ingreso' => old('ingresos')[$i] ?? '',
                'facultad' => old('facultads')[$i] ?? '',
                'universidad' => old('universidads')[$i] ?? '',
                'activo' => isset(old('activos', [])[$i]),
            ];
        }
    } else {
        foreach ($investigador->cargos as $c) {
            $filasCargos[] = [
                'cargo' => $c->pivot->cargo_id,
                'deddoc' => $c->pivot->deddoc,
                'ingreso' => $fechaIso($c->pivot->ingreso),
                'facultad' => $c->pivot->facultad_id,
                'universidad' => $c->pivot->universidad_id,
                'activo' => (bool) $c->pivot->activo,
            ];
        }
    }

    // --- Carrera de investigacion ---
    $filasCarreras = [];
    $actualSel = old('actual');
    if (is_array(old('carrerainvs'))) {
        foreach (old('carrerainvs') as $i => $v) {
            $filasCarreras[] = [
                'carrerainv' => $v,
                'organismo' => old('organismos')[$i] ?? '',
                'ingreso' => old('carringresos')[$i] ?? '',
            ];
        }
    } else {
        $actualSel = '0';
        foreach ($investigador->carrerainvs as $i => $c) {
            $filasCarreras[] = [
                'carrerainv' => $c->pivot->carrerainv_id,
                'organismo' => $c->pivot->organismo_id,
                'ingreso' => $fechaIso($c->pivot->ingreso),
            ];
            if ($c->pivot->actual) {
                $actualSel = (string) ($i + 1);
            }
        }
    }

    // --- Categorias SPU ---
    $filasCategorias = [];
    $catActualSel = old('catactual');
    if (is_array(old('categorias'))) {
        foreach (old('categorias') as $i => $v) {
            $filasCategorias[] = [
                'categoria' => $v,
                'year' => old('catyears')[$i] ?? '',
                'notificacion' => old('catnotificacions')[$i] ?? '',
                'universidad' => old('catuniversidads')[$i] ?? '',
            ];
        }
    } else {
        $catActualSel = '0';
        foreach ($investigador->categorias as $i => $c) {
            $filasCategorias[] = [
                'categoria' => $c->pivot->categoria_id,
                'year' => $c->pivot->year,
                'notificacion' => $fechaIso($c->pivot->notificacion),
                'universidad' => $c->pivot->universidad_id,
            ];
            if ($c->pivot->actual) {
                $catActualSel = (string) ($i + 1);
            }
        }
    }

    // --- Categorias SICADI ---
    $filasSicadis = [];
    $sicadiActualSel = old('sicadiactual');
    if (is_array(old('sicadis'))) {
        foreach (old('sicadis') as $i => $v) {
            $filasSicadis[] = [
                'sicadi' => $v,
                'year' => old('sicadiyears')[$i] ?? '',
                'notificacion' => old('sicadinotificacions')[$i] ?? '',
            ];
        }
    } else {
        $sicadiActualSel = '0';
        foreach ($investigador->sicadis as $i => $s) {
            $filasSicadis[] = [
                'sicadi' => $s->pivot->sicadi_id,
                'year' => $s->pivot->year,
                'notificacion' => $fechaIso($s->pivot->notificacion),
            ];
            if ($s->pivot->actual) {
                $sicadiActualSel = (string) ($i + 1);
            }
        }
    }

    // --- Becas ---
    $filasBecas = [];
    if (is_array(old('institucions'))) {
        foreach (old('institucions') as $i => $v) {
            $filasBecas[] = [
                'institucion' => $v,
                'beca' => old('becas')[$i] ?? '',
                'desde' => old('becadesdes')[$i] ?? '',
                'hasta' => old('becahastas')[$i] ?? '',
                'unlp' => isset(old('becaunlps', [])[$i]),
            ];
        }
    } else {
        foreach ($investigador->becas as $b) {
            $filasBecas[] = [
                'institucion' => $b->institucion,
                'beca' => $b->beca,
                'desde' => $fechaIso($b->desde),
                'hasta' => $fechaIso($b->hasta),
                'unlp' => (bool) $b->unlp,
            ];
        }
    }
@endphp
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-microscope" aria-hidden="true"></i>Investigador
                <small>Editar</small>
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
                        <form role="form" action="{{ route('investigadors.update',$investigador->id) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
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
                                                    {{Form::text('apellido', $investigador->persona->apellido, ['class' => 'form-control','placeholder'=>'Apellido'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('nombre', 'Nombre')}}
                                                    {{Form::text('nombre', $investigador->persona->nombre, ['class' => 'form-control','placeholder'=>'Nombre'])}}
                                                </div>
                                            </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('documento', 'Documento')}}
                                                {{Form::number('documento', $investigador->persona->documento, ['class' => 'form-control','placeholder'=>'Documento'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('cuil', 'CUIL')}}
                                                {{Form::text('cuil', $investigador->persona->cuil, ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X'])}}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                {{Form::label('email', 'Email')}}
                                                {{Form::email('email', $investigador->persona->email, ['class' => 'form-control','placeholder'=>'Email'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {{Form::label('genero', 'Género')}}
                                                {{ Form::select('genero',[''=>'Seleccionar...','F'=>'Mujer','MT'=>'Mujer-Trans','T'=>'Travesti','M'=>'Varón','VY'=>'Varón-Trans','NB'=>'No Binarie','O'=>'Otro','PN'=>'Prefiero no responder'], $investigador->persona->genero,['class' => 'form-control','id'=>'genero']) }}

                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('nacimiento', 'Nacimiento')}}
                                                {{Form::date('nacimiento', ($investigador->persona->nacimiento)?date('Y-m-d', strtotime($investigador->persona->nacimiento)):'', ['class' => 'form-control'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('fallecimiento', 'Fallecimiento')}}
                                                {{Form::date('fallecimiento', ($investigador->persona->fallecimiento)?date('Y-m-d', strtotime($investigador->persona->fallecimiento)):'', ['class' => 'form-control'])}}
                                            </div>
                                        </div>
                                    </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('telefono', 'Teléfono')}}
                                                    {{Form::text('telefono', $investigador->persona->telefono, ['class' => 'form-control','placeholder'=>'Teléfono'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('calle', 'Calle')}}
                                                    {{Form::text('calle', $investigador->persona->calle, ['class' => 'form-control','placeholder'=>'Calle'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('nro', 'Número')}}
                                                    {{Form::text('nro', $investigador->persona->nro, ['class' => 'form-control','placeholder'=>'Número'])}}

                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('piso', 'Piso')}}
                                                    {{Form::text('piso', $investigador->persona->piso, ['class' => 'form-control','placeholder'=>'Piso'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('depto', 'Departamento')}}
                                                    {{Form::text('depto', $investigador->persona->depto, ['class' => 'form-control','placeholder'=>'Departamento'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('localidad', 'Localidad')}}
                                                    {{Form::text('localidad', $investigador->persona->localidad, ['class' => 'form-control','placeholder'=>'Localidad'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('provincia', 'Provincia')}}
                                                    {{Form::select('provincia_id', ['' => 'Seleccionar...'] + $provincias->toArray(),($investigador->persona->provincia)?$investigador->persona->provincia->id:'', ['class' => 'form-control js-example-basic-single','id'=>'provincia_id'])}}

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('cp', 'Código Postal')}}
                                                    {{Form::text('cp', $investigador->persona->cp, ['class' => 'form-control','placeholder'=>'Código Postal'])}}
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">

                                                <div class="form-group">
                                                    <label for="foto">Foto</label>
                                                    @if($investigador->persona->foto)
                                                        <img id="original" src="{{ url('images/'.$investigador->persona->foto) }}" height="200">
                                                    @endif
                                                    <input type="file" name="foto" class="form-control" placeholder="">

                                                </div>
                                            </div>
                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    {{Form::label('observaciones', 'Observaciones')}}
                                                    {{Form::textarea('observaciones', $investigador->persona->observaciones, ['class' => 'form-control'])}}

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
                                                @foreach ($filasTitulos as $fila)
                                                <tr>

                                                    <td>{!! $selectFila('titulos[]', $titulos, $fila['titulo'], 'form-control js-example-basic-single', 'width: 400px') !!}</td>
                                                    <td>{!! $fechaFila('egresos[]', $fila['egreso']) !!}</td>

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
                                                    {{Form::text('carrera', $investigador->carrera, ['class' => 'form-control','placeholder'=>'Carrera'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('total', 'Total De Materias')}}
                                                    {{Form::number('total', $investigador->total, ['class' => 'form-control','placeholder'=>'Total De Materias'])}}

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('materias', 'Aprobadas')}}
                                                    {{Form::number('materias', $investigador->materias, ['class' => 'form-control','placeholder'=>'Aprobadas'])}}
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
                                                    @foreach ($filasPosgrados as $fila)
                                                        <tr>

                                                            <td>{!! $selectFila('tituloposts[]', $tituloposts, $fila['titulo'], 'form-control js-example-basic-single', 'width: 400px') !!}</td>
                                                            <td>{!! $fechaFila('egresoposts[]', $fila['egreso']) !!}</td>

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
                                                    @foreach ($filasCargos as $fila)

                                                    <tr>

                                                        <td>{!! $selectFila('cargos[]', $cargos, $fila['cargo'], 'form-control', 'width: 200px') !!}</td>

                                                        <td>{!! $selectFila('deddocs[]', ['' => ''] + $dedicaciones, $fila['deddoc'], 'form-control', 'width: 120px') !!}</td>
                                                        <td>{!! $fechaFila('ingresos[]', $fila['ingreso']) !!}</td>
                                                        <td>{!! $selectFila('facultads[]', $facultades, $fila['facultad'], 'form-control', 'width: 300px') !!}</td>
                                                        <td>{!! $selectFila('universidads[]', $universidades, $fila['universidad'], 'form-control js-example-basic-single', 'width: 300px') !!}</td>
                                                        <td><input type="checkbox" name="activos[]" value="1" {{ $fila['activo'] ? 'checked' : '' }}></td>
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
                                                    {{Form::select('unidad_id',  $unidads,$investigador->unidad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidad_id'])}}

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
                                                    @foreach ($filasCarreras as $i => $fila)
                                                    <tr>

                                                        <td>{!! $selectFila('carrerainvs[]', $carrerainvs, $fila['carrerainv'], 'form-control', 'width: 200px') !!}</td>
                                                        <td>{!! $selectFila('organismos[]', $organismos, $fila['organismo'], 'form-control', 'width: 150px') !!}</td>
                                                        <td>{!! $fechaFila('carringresos[]', $fila['ingreso']) !!}</td>


                                                        <td><input type="radio" name="actual" id="actual_{{ $i + 1 }}" value="{{ $i + 1 }}" {{ (string) $actualSel === (string) ($i + 1) ? 'checked' : '' }}></td>
                                                        <td><a href="#" class="btn btn-danger removeCarrerainv"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="3" style="text-align: right; font-style: italic;">Ninguna (sin carrera actual)</td>
                                                        <td><input type="radio" name="actual" id="actual_0" value="0" {{ (string) $actualSel === '0' ? 'checked' : '' }}></td>
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
                                                    @foreach ($filasCategorias as $i => $fila)
                                                    <tr>

                                                        <td>{!! $selectFila('categorias[]', $categorias, $fila['categoria'], 'form-control', 'width: 60px') !!}</td>
                                                        <td>{!! $selectFila('catyears[]', ['' => ''] + $years, $fila['year'], 'form-control', 'width: 80px') !!}</td>
                                                        <td>{!! $fechaFila('catnotificacions[]', $fila['notificacion']) !!}</td>
                                                        <td>{!! $selectFila('catuniversidads[]', $universidades, $fila['universidad'], 'form-control js-example-basic-single', 'width: 300px') !!}</td>

                                                        <td><input type="radio" name="catactual" id="catactual_{{ $i + 1 }}" value="{{ $i + 1 }}" {{ (string) $catActualSel === (string) ($i + 1) ? 'checked' : '' }}></td>
                                                        <td><a href="#" class="btn btn-danger removeCategoria"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="4" style="text-align: right; font-style: italic;">Ninguna (sin categoría actual)</td>
                                                        <td><input type="radio" name="catactual" id="catactual_0" value="0" {{ (string) $catActualSel === '0' ? 'checked' : '' }}></td>
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
                                                    @foreach ($filasSicadis as $i => $fila)
                                                    <tr>

                                                        <td>{!! $selectFila('sicadis[]', $sicadis, $fila['sicadi'], 'form-control', 'width: 120px') !!}</td>
                                                        <td>{!! $selectFila('sicadiyears[]', ['' => ''] + $years, $fila['year'], 'form-control', 'width: 80px') !!}</td>
                                                        <td>{!! $fechaFila('sicadinotificacions[]', $fila['notificacion']) !!}</td>


                                                        <td><input type="radio" name="sicadiactual" id="sicadiactual_{{ $i + 1 }}" value="{{ $i + 1 }}" {{ (string) $sicadiActualSel === (string) ($i + 1) ? 'checked' : '' }}></td>
                                                        <td><a href="#" class="btn btn-danger removeSicadi"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                    </tr>
                                                    @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td colspan="3" style="text-align: right; font-style: italic;">Ninguna (sin categoría actual)</td>
                                                        <td><input type="radio" name="sicadiactual" id="sicadiactual_0" value="0" {{ (string) $sicadiActualSel === '0' ? 'checked' : '' }}></td>
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

                                                    @foreach ($filasBecas as $fila)
                                                    <tr>

                                                        <td>{!! $selectFila('institucions[]', $institucionesBeca, $fila['institucion'], 'form-control institucion_select', 'width: 150px') !!}</td>
                                                        <td>{!! $selectFila('becas[]', $tiposBeca, $fila['beca'], 'form-control beca_select', 'width: 150px') !!}</td>

                                                        <td>{!! $fechaFila('becadesdes[]', $fila['desde']) !!}</td>

                                                        <td>{!! $fechaFila('becahastas[]', $fila['hasta']) !!}</td>
                                                        <td><input type="checkbox" name="becaunlps[]" value="1" {{ $fila['unlp'] ? 'checked' : '' }}></td>
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
        // que ya existen (del modelo o de un POST rebotado) las dibuja Blade arriba.
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

// Verificar si hay títulos presentes al cargar la página
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
                // Renumber radios after removal
                $('#cuerpoCarrerainvs tr').each(function(i){
                    $(this).find('input[type="radio"][name="actual"]')
                        .attr('id', 'actual_' + (i + 1))
                        .attr('value', i + 1);
                });
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
       /* $(document).on('change', 'select.institucion_select', function() {
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
        }*/


    </script>

@endsection
