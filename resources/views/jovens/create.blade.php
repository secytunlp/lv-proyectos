@extends('layouts.app')
@section('headSection')

    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">


@endsection


@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-microscope" aria-hidden="true"></i>Jóvenes Investigadores
                <small>Crear</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('jovens.index') }}">Jóvenes Investigadores</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Crear</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('jovens.store') }}" method="post" enctype="multipart/form-data" novalidate>
                            {{ csrf_field() }}
                            <input type="hidden" name="investigador_id" value="{{ $investigador->id ?? '' }}">
                            <input type="hidden" name="periodo_id" value="{{ $periodo->id ?? '' }}">
                            <div class="box-body">
                                @include('includes.messages')
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#datos_personales" aria-controls="datos_personales" role="tab" data-toggle="tab">Datos Personales</a></li>
                                    <li role="presentation"><a href="#universidad" aria-controls="universidad" role="tab" data-toggle="tab">Universidad</a></li>
                                    <li role="presentation"><a href="#investigacion" aria-controls="investigacion" role="tab" data-toggle="tab">Investigación</a></li>
                                    <!--<li role="presentation"><a href="#categorizacion" aria-controls="categorizacion" role="tab" data-toggle="tab">Categorización</a></li>-->
                                    <li role="presentation"><a href="#becario" aria-controls="becario" role="tab" data-toggle="tab">Becas</a></li>
                                    <li role="presentation"><a href="#proyectos" aria-controls="proyectos" role="tab" data-toggle="tab">Proyectos</a></li>
                                    <li role="presentation"><a href="#presupuesto" aria-controls="presupuesto" role="tab" data-toggle="tab">Presupuesto</a></li>
                                    <li role="presentation"><a href="#descripcion" aria-controls="descripcion" role="tab" data-toggle="tab">Descripción</a></li>
                                    <!-- Agrega más pestañas según sea necesario -->
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="datos_personales">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('apellido', 'Apellido')}}
                                                    {{Form::text('apellido', $investigador->persona->apellido, ['class' => 'form-control','placeholder'=>'Apellido','disabled'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('nombre', 'Nombre')}}
                                                    {{Form::text('nombre', $investigador->persona->nombre, ['class' => 'form-control','placeholder'=>'Nombre','disabled'])}}
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('cuil', 'CUIL')}}
                                                    {{Form::text('cuil', $investigador->persona->cuil, ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X','disabled'])}}
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
                                                    {{Form::label('telefono', 'Teléfono')}}
                                                    {{Form::text('telefono', $investigador->persona->telefono, ['class' => 'form-control','placeholder'=>'Teléfono'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('nacimiento', 'Nacimiento')}}
                                                    {{Form::date('nacimiento', ($investigador->persona->nacimiento)?date('Y-m-d', strtotime($investigador->persona->nacimiento)):'', ['class' => 'form-control'])}}
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    {{Form::label('notificacion', 'Acepto recibir toda notificación relativa a la presente solicitud
en la dirección de correo electrónico declarada precedentemente')}}
                                                    {{Form::checkbox('notificacion', 1,true)}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
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
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('cp', 'Código Postal')}}
                                                    {{Form::text('cp', $investigador->persona->cp, ['class' => 'form-control','placeholder'=>'Código Postal'])}}
                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="universidad">
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Título de Grado</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>

                                                        <th>Título</th>
                                                        <th>Egreso</th>
                                                        <!--<th><a href="#" class="addRow"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                        </thead>

                                                        <tbody id="cuerpoTitulo">
                                                        <tr>

                                                            <td>{{ Form::select('titulos[]',$titulos, ($titulo)?$titulo->id:'',['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px']) }}</td>
                                                            <td>{{Form::date('egresos[]', ($titulo) ?
            (($titulo->pivot->egreso) ? date('Y-m-d', strtotime($titulo->pivot->egreso)) : '')
            : '',  ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>

                                                            <!--<td><a href="#" class="btn btn-danger remove"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>

                                        </fieldset>


                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Título de Posgrado</legend>

                                            <div class="form-group col-md-12">
                                                <div class="table-responsive">

                                                    <table class="table" style="width: 50%">
                                                        <thead>

                                                        <th>Título</th>
                                                        <th>Egreso</th>
                                                        <th>Doctorado</th>
                                                        <!--<th><a href="#" class="addRowPost"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                        </thead>

                                                        <tbody id="cuerpoPosgrado">
                                                        <tr>

                                                            <td>{{ Form::select('tituloposts[]',$tituloposts, ($titulopost)?$titulopost->id:'',['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px']) }}</td>
                                                            <td>{{Form::date('egresoposts[]', ($titulopost) ?
            (($titulopost->pivot->egreso) ? date('Y-m-d', strtotime($titulopost->pivot->egreso)) : '')
            : '',  ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                            <td>{{Form::checkbox('doctorados[]', 1,false)}}</td>
                                                            <!--<td><a href="#" class="btn btn-danger removePost"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Cargo Docente</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>

                                                        <th>Cargo</th>
                                                        <th>Dedicación</th>
                                                        <th>Ingreso</th>
                                                        <th>U. Académica</th>
                                                        <!--<th>Universidad</th>
                                                        <th>Activo</th>
                                                        <th><a href="#" class="addRowCargo"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                        </thead>

                                                        <tbody id="cuerpoCargos">
                                                        <tr>
                                                            @php
                                                                $dedicaciones = config('dedicaciones');
                                                                unset($dedicaciones['Sin Dedicación']);
                                                            @endphp
                                                            <td>{{ Form::select('cargos[]',$cargos, ($cargo)?$cargo->id:'',['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                            <td>{{ Form::select('deddocs[]',['' => ''] + $dedicaciones, ($cargo)?$cargo->pivot->deddoc:'',['class' => 'form-control', 'style' => 'width: 120px']) }}</td>
                                                            <td>{{Form::date('ingresos[]', ($cargo) ?
            (($cargo->pivot->ingreso) ? date('Y-m-d', strtotime($cargo->pivot->ingreso)) : '')
            : '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                            <td>{{ Form::select('facultads[]',$facultades, ($cargo)?$cargo->pivot->facultad_id:'',['class' => 'form-control', 'style' => 'width: 300px']) }}</td>
                                                            <!--<td>{{ Form::select('universidads[]',$universidades, '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px']) }}</td>
                                                        <td>{{Form::checkbox('activos[]', 1,true)}}</td>
                                                        <td><a href="#" class="btn btn-danger removeCargo"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    {{Form::label('unidad', 'Lugar de Trabajo en la UNLP (Unidad de Investigación: Laboratorio, Centro, Instituto, etc.)')}}
                                                    {{Form::select('unidad_id',  $unidads,$investigador->unidad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidad_id'])}}

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('disciplina', 'Disciplina')}}
                                                    {{Form::text('disciplina', '', ['class' => 'form-control','placeholder'=>'Disciplina'])}}

                                                </div>
                                            </div>


                                        </div>
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
                                                        <th>Ingreso</th>

                                                        <!--<th>Actual</th>
                                                        <th><a href="#" class="addRowCarrerainv"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                        </thead>

                                                        <tbody id="cuerpoCarrerainvs">
                                                        <tr>

                                                            <td>{{ Form::select('carrerainvs[]',$carrerainvs, ($carrerainv)?$carrerainv->id:'',['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                            <td>{{ Form::select('organismos[]',$organismos, ($carrerainv)?$carrerainv->pivot->organismo_id:'',['class' => 'form-control', 'style' => 'width: 150px']) }}</td>
                                                            <td>{{Form::date('carringresos[]', ($carrerainv) ?
            (($carrerainv->pivot->ingreso) ? date('Y-m-d', strtotime($carrerainv->pivot->ingreso)) : '')
            : '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>


                                                            <!--<td>{{ Form::radio('actual', 1, true,['id' => 'actual_1']) }}</td>
                                                        <td><a href="#" class="btn btn-danger removeCarrerainv"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        {{Form::label('unidad', 'Lugar de Trabajo')}}
                                                        {{Form::select('unidadcarrera_id',  $unidads,'', ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidadcarrera_id'])}}

                                                    </div>
                                                </div>


                                            </div>
                                        </fieldset>
                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="becario">

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Beca actual</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>


                                                        <th>Institución</th>
                                                        <th>Beca</th>
                                                        <th>Desde</th>
                                                        <th>Hasta</th>
                                                        <!--<th>UNLP</th>-->
                                                        <!--<th><a href="#" class="addRowBeca"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                        </thead>

                                                        <tbody id="cuerpoBecaActual">
                                                        <tr>

                                                            <td>{{ Form::select('institucionActual',[''=>'','ANPCyT'=>'ANPCyT','CIC'=>'CIC','CONICET'=>'CONICET','UNLP'=>'UNLP','CIN'=>'CIN','OTRA'=>'OTRA'], ($beca)?$beca->institucion:'',['class' => 'form-control institucionActual_select', 'style' => 'width: 150px']) }}</td>
                                                            <!--<td>{{ Form::select('becaActual',[''=>'','Beca inicial'=>'Beca inicial','Beca superior'=>'Beca superior','Beca de entrenamiento'=>'Beca de entrenamiento','Beca doctoral'=>'Beca doctoral','Beca posdoctoral'=>'Beca posdoctoral','Beca finalización del doctorado'=>'Beca finalización del doctorado','Beca maestría'=>'Beca maestría','Beca Cofinanciada (UNLP-CIC)'=>'Beca Cofinanciada (UNLP-CIC)','EVC'=>'EVC'], ($beca)?$beca->beca:'',['class' => 'form-control becaActual_select', 'style' => 'width: 150px']) }}</td>-->
                                                            <td>{{ Form::select('becaActual', \App\Helpers\BecaHelper::obtenerOpcionesBecaPorInstitucion(($beca)?$beca->institucion:''), ($beca)?$beca->beca:'', ['class' => 'form-control becaActual_select', 'style' => 'width: 150px']) }}
                                                            </td>
                                                            <td>{{Form::date('becadesdeActual', ($beca) ?
                                                            (($beca->desde) ? date('Y-m-d', strtotime($beca->desde)) : ''): '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>

                                                            <td>{{Form::date('becahastaActual', ($beca) ?
                                                            (($beca->hasta) ? date('Y-m-d', strtotime($beca->hasta)) : ''): '', ['class' => 'form-control', 'style' => 'width:150px;'])}}  {{Form::hidden('unlpActual', ($beca)?$beca->unlp:0)}}</td>

                                                            <!--<td><a href="#" class="btn btn-danger removeCategoria"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                        </tr>

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>
                                            <div class="row">

                                                <div class="col-md-8">
                                                    <div class="form-group">
                                                        {{Form::label('unidad', 'Lugar de Trabajo')}}
                                                        {{Form::select('unidadbeca_id',  $unidads,'', ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidadbeca_id'])}}

                                                    </div>
                                                </div>


                                            </div>
                                        </fieldset>

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Becas anteriores</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 50%">
                                                        <thead>


                                                        <th>Institución</th>
                                                        <th>Beca</th>
                                                        <th>Desde</th>
                                                        <th>Hasta</th>
                                                        <!--<th>UNLP</th>-->
                                                        <th><a href="#" class="addRowBeca"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                        </thead>

                                                        <tbody id="cuerpoBecas">
                                                        @php
                                                            // Obtener los datos viejos si existen
                                                            $institucions = old('institucions', $becas->pluck('institucion')->toArray());

                                                            $becasSeleccionadas = old('becas', $becas->pluck('beca')->toArray());
                                                            $becadesdes = old('becadesdes', $becas->pluck('desde')->map(function($fecha) {
                                                                return $fecha ? date('Y-m-d', strtotime($fecha)) : '';
                                                            })->toArray());
                                                            $becahastas = old('becahastas', $becas->pluck('hasta')->map(function($fecha) {
                                                                return $fecha ? date('Y-m-d', strtotime($fecha)) : '';
                                                            })->toArray());
                                                            $becaagregadas = old('becaagregadas', $becas->pluck('agregada')->toArray());

                                                        @endphp

                                                        @foreach ($institucions as $index => $institucion)

                                                            <tr>
                                                                <td><select class="form-control institucion_select" style="width: 150px" name="institucions[]">
                                                                        <option value=""></option>
                                                                        <option value="ANPCyT" {{ $institucion == 'ANPCyT' ? 'selected' : '' }}>ANPCyT</option>
                                                                        <option value="CIC" {{ $institucion == 'CIC' ? 'selected' : '' }}>CIC</option>
                                                                        <option value="CONICET" {{ $institucion == 'CONICET' ? 'selected' : '' }}>CONICET</option>
                                                                        <option value="UNLP" {{ $institucion == 'UNLP' ? 'selected' : '' }}>UNLP</option>
                                                                        <option value="CIN" {{ $institucion == 'CIN' ? 'selected' : '' }}>CIN</option>
                                                                        <option value="OTRA" {{ $institucion == 'OTRA' ? 'selected' : '' }}>OTRA</option>
                                                                    </select>
                                                                </td>
                                                                <td>{{ Form::select('becas[]', \App\Helpers\BecaHelper::obtenerOpcionesBecaPorInstitucionAnterior($institucion), $becasSeleccionadas[$index], ['class' => 'form-control beca_select', 'style' => 'width: 150px']) }}
                                                                </td>
                                                                <td>{{ Form::date('becadesdes[]', $becadesdes[$index], ['class' => 'form-control', 'style' => 'width:150px;']) }}</td>
                                                                <td>{{ Form::date('becahastas[]', $becahastas[$index], ['class' => 'form-control', 'style' => 'width:150px;']) }}
                                                                    {{ Form::hidden('becaagregadas[]', $becaagregadas[$index]) }}
                                                                </td>
                                                                <td><a href="#" class="btn btn-danger removeBeca"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                            </tr>
                                                        @endforeach

                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>

                                        </fieldset>

                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="proyectos">
                                        <div class="row" style="margin: 10px;">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    {{Form::label('director', 'Es o ha sido DIR./CODIR. de proyectos de acreditación')}}
                                                    {{Form::checkbox('director', 1,false)}}
                                                </div>
                                            </div>
                                        </div>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Actuales</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 100%">
                                                        <thead>


                                                        <th>Código</th>
                                                        <th>Título</th>
                                                        <th>Director</th>
                                                        <th>Inicio</th>
                                                        <th>Fin</th>
                                                        <th>Estado</th>

                                                        </thead>

                                                        <tbody id="cuerpoProyectoActual">
                                                        @foreach ($proyectosActuales as $proyectoActual)
                                                            <tr>
                                                                <td>{{Form::text('codigoActual[]', $proyectoActual['codigo'], ['class' => 'form-control', 'style' => 'width:150px;','disabled'])}}</td>
                                                                <td>{{Form::textarea('tituloActual[]', $proyectoActual['titulo'], ['class' => 'form-control', 'style' => 'width:450px;','disabled','rows'=>2])}}</td>
                                                                <td>{{Form::text('directorActual[]', $proyectoActual['director'], ['class' => 'form-control', 'style' => 'width:200px;','disabled'])}}</td>
                                                                <td>{{Form::date('inicioActual[]', ($proyectoActual['inicio'])?date('Y-m-d', strtotime($proyectoActual['inicio'])):'', ['class' => 'form-control', 'style' => 'width:120px;','disabled'])}}</td>
                                                                <td>{{Form::date('finActual[]', ($proyectoActual['fin'])?date('Y-m-d', strtotime($proyectoActual['fin'])):'', ['class' => 'form-control', 'style' => 'width:120px;','disabled'])}}</td>
                                                                <td>{{Form::text('estadoActual[]', $proyectoActual['estado'], ['class' => 'form-control', 'style' => 'width:150px;','disabled'])}}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>

                                        </fieldset>

                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Anteriores (Si formó parte de un proyecto que finalizó antes del 31/12/2009 debe ingresarlo)</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                    <table class="table" style="width: 100%">
                                                        <thead>


                                                        <th>Código</th>
                                                        <th>Título</th>
                                                        <th>Director</th>
                                                        <th>Inicio</th>
                                                        <th>Fin</th>

                                                        <th><a href="#" class="addRowProyecto"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                        </thead>

                                                        <tbody id="cuerpoProyectos">
                                                        @foreach ($proyectosAnteriores as $proyectoAnt)
                                                            <tr>

                                                                <td>{{Form::text('codigoAnteriorOld[]', $proyectoAnt['codigo'], ['class' => 'form-control', 'style' => 'width:150px;','disabled'])}}</td>
                                                                <td>{{Form::textarea('tituloAnteriorOld[]', $proyectoAnt['titulo'], ['class' => 'form-control', 'style' => 'width:450px;','disabled','rows'=>2])}}</td>
                                                                <td>{{Form::text('directorAnteriorOld[]', $proyectoAnt['director'], ['class' => 'form-control', 'style' => 'width:200px;','disabled'])}}</td>
                                                                <td>{{Form::date('inicioAnteriorOld[]', ($proyectoAnt['inicio'])?date('Y-m-d', strtotime($proyectoAnt['inicio'])):'', ['class' => 'form-control', 'style' => 'width:150px;','disabled'])}}</td>
                                                                <td>{{Form::date('finAnteriorOld[]', ($proyectoAnt['fin'])?date('Y-m-d', strtotime($proyectoAnt['fin'])):'', ['class' => 'form-control', 'style' => 'width:150px;','disabled'])}}</td>
                                                                <!--<td><a href="#" class="btn btn-danger removeProyecto"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                            </tr>
                                                        @endforeach
                                                        @php
                                                            // Obtener los datos viejos si existen
                                                            $codigosAnterioresOld = old('codigoAnterior', []);
                                                            $titulosAnterioresOld = old('tituloAnterior', []);
                                                            $directoresAnterioresOld = old('directorAnterior', []);
                                                            $iniciosAnterioresOld = old('inicioAnterior', []);
                                                            $finesAnterioresOld = old('finAnterior', []);
                                                            $totalOlds = count($codigosAnterioresOld);
                                                        @endphp

                                                        @for ($i = 0; $i < $totalOlds; $i++)
                                                            @php
                                                                // Reemplazar valores viejos con valores vacíos si no existen en el old
                                                                $codigo = $codigosAnterioresOld[$i] ?? '';
                                                                $titulo = $titulosAnterioresOld[$i] ?? '';
                                                                $director = $directoresAnterioresOld[$i] ?? '';
                                                                $inicio = $iniciosAnterioresOld[$i] ?? '';
                                                                $fin = $finesAnterioresOld[$i] ?? '';
                                                            @endphp

                                                            <tr>
                                                                <td>{{ Form::text('codigoAnterior[]', $codigo, ['class' => 'form-control', 'style' => 'width:150px;']) }}</td>
                                                                <td>{{ Form::textarea('tituloAnterior[]', $titulo, ['class' => 'form-control', 'style' => 'width:450px;', 'rows' => 2]) }}</td>
                                                                <td>{{ Form::text('directorAnterior[]', $director, ['class' => 'form-control', 'style' => 'width:200px;']) }}</td>
                                                                <td>{{ Form::date('inicioAnterior[]', $inicio, ['class' => 'form-control', 'style' => 'width:150px;']) }}</td>
                                                                <td>{{ Form::date('finAnterior[]', $fin, ['class' => 'form-control', 'style' => 'width:150px;']) }}</td>
                                                     <td><a href="#" class="btn btn-danger removeProyecto"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                            </tr>
                                                        @endfor
                                                        </tbody>




                                                    </table>
                                                </div>
                                            </div>

                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="descripcion">
                                        <div class="row" style="margin: 10px;">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    {{Form::label('facu', 'Debe seleccionar la Unidad Académica donde Ud. realiza la actividad de I+D')}}

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('facultadplanilla', 'U. Académica por la que presenta la solicitud')}}
                                                    {{Form::select('facultadplanilla_id',  $facultades,'', ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'facultadplanilla_id'])}}

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    {{Form::label('objetivo', 'Breve descripción de las actividades de I/D que plantea en el marco del proyecto en que se desempeña el solicitante')}}
                                                    {{Form::textarea('objetivo', '', ['class' => 'form-control'])}}

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    {{Form::label('justificacion', 'Justificar el pedido de fondos detallado en el presupuesto preliminar. Además, para cada ítem que solicita en el presupuesto preliminar deberá
a) detallar el mismo y b) justificar su pedido. En el caso de solicitar bibliografía deberá indicar título, autor, editorial, etc.')}}
                                                    {{Form::textarea('justificacion', '', ['class' => 'form-control'])}}

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="curriculum">Curriculum</label>
                                                    <input type="file" name="curriculum" class="form-control" placeholder="">

                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="actividades" style="color: #a90062">IMPORTANTE: El CV deberá ser el generado (pdf/doc) por el sistema SIGEVA-UNLP (banco de datos de
                                                        actividades de ciencia y técnica)</label>


                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="presupuesto">
                                        @foreach ($tipoPresupuestos as $tipoPresupuesto)
                                            <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                                <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">{{$tipoPresupuesto->nombre}}</legend>

                                                <div class="form-group col-md-12">

                                                    <div class="table-responsive">
                                                        <table class="table" style="width: 70%">
                                                            <thead>


                                                            <th>Fecha</th>
                                                            <th>Descripción/Concepto</th>
                                                            <th>Importe</th>

                                                            <th><a href="#" class="addRowPresupuesto" data-tipo-id="{{$tipoPresupuesto->id}}"><i class="glyphicon glyphicon-plus"></i></a></th>
                                                            </thead>
                                                            <tbody class="cuerpoPresupuesto" data-tipo-id="{{$tipoPresupuesto->id}}">
                                                            @if(old('presupuesto'.$tipoPresupuesto->id.'fechas'))
                                                                @foreach(old('presupuesto'.$tipoPresupuesto->id.'fechas') as $index => $fecha)
                                                                    <tr>
                                                                        <td>{{Form::date('presupuesto'.$tipoPresupuesto->id.'fechas[]', $fecha, ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                                        @if($tipoPresupuesto->id == 2)
                                                                            <td>
                                                                                <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                                                                                    {{ Form::select('presupuesto'.$tipoPresupuesto->id.'conceptos[]',
                                                                                        [''=>'','Viaticos'=>'Viáticos','Pasajes'=>'Pasajes','Inscripcion'=>'Inscripción','Otros'=>'Otros'],
                                                                                        old('presupuesto'.$tipoPresupuesto->id.'conceptos.'.$index),
                                                                                        ['class' => 'form-control', 'style' => 'width: 120px', 'onchange' => 'seleccionarConcepto(this)']) }}

                                                                                    {{-- Contenedor para campos adicionales --}}
                                                                                    <div class="extra-fields" style="display: flex; gap: 10px; align-items: center;">
                                                                                        @if(old('presupuesto'.$tipoPresupuesto->id.'conceptos.'.$index)=== 'Viaticos')
                                                                                            {{ Form::text('presupuesto'.$tipoPresupuesto->id.'dias[]', old('presupuesto'.$tipoPresupuesto->id.'dias.'.$index),
                                                                                                ['class' => 'form-control ds_dias', 'placeholder' => 'Días', 'style' => 'width:150px'])
                                                                                            }}

                                                                                            {{ Form::text('presupuesto'.$tipoPresupuesto->id.'lugar[]', old('presupuesto'.$tipoPresupuesto->id.'lugar.'.$index),
                                                                                                ['class' => 'form-control ds_lugar', 'placeholder' => 'Lugar', 'style' => 'width:150px'])
                                                                                            }}
                                                                                        @elseif(old('presupuesto'.$tipoPresupuesto->id.'conceptos.'.$index) === 'Pasajes')
                                                                                            {{ Form::select('presupuesto'.$tipoPresupuesto->id.'pasajes[]',
                                                                                                ['' => '', 'Aereo' => 'Aéreo', 'Omnibus' => 'Omnibus', 'Automovil' => 'Automóvil'],
                                                                                                old('presupuesto'.$tipoPresupuesto->id.'pasajes.'.$index),
                                                                                                ['class' => 'form-control ds_pasajes', 'style' => ' width:120px']
                                                                                            ) }}

                                                                                            {{ Form::text('presupuesto'.$tipoPresupuesto->id.'destino[]', old('presupuesto'.$tipoPresupuesto->id.'destino.'.$index),
                                                                                                ['class' => 'form-control ds_destino', 'placeholder' => 'Destino', 'style' => 'width:150px'])
                                                                                            }}
                                                                                        @elseif(old('presupuesto'.$tipoPresupuesto->id.'conceptos.'.$index) === 'Inscripcion')
                                                                                            {{ Form::text('presupuesto'.$tipoPresupuesto->id.'inscripcion[]', old('presupuesto'.$tipoPresupuesto->id.'inscripcion.'.$index),
                                                                                                ['class' => 'form-control ds_inscripcion', 'placeholder' => 'Descripción', 'style' => 'width:150px'])
                                                                                            }}
                                                                                        @elseif(old('presupuesto'.$tipoPresupuesto->id.'conceptos.'.$index) === 'Otros')
                                                                                            {{ Form::text('presupuesto'.$tipoPresupuesto->id.'otros[]', old('presupuesto'.$tipoPresupuesto->id.'otros.'.$index),
                                                                                                ['class' => 'form-control ds_otros', 'placeholder' => 'Otros', 'style' => 'width:150px'])
                                                                                            }}
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </td>
                                                                        @else
                                                                            <td>{{ Form::text('presupuesto'.$tipoPresupuesto->id.'detalles[]', old('presupuesto'.$tipoPresupuesto->id.'detalles.'.$index), ['class' => 'form-control', 'style' => 'width: 400px']) }}</td>
                                                                        @endif
                                                                        <td>{{Form::number('presupuesto'.$tipoPresupuesto->id.'importes[]', old('presupuesto'.$tipoPresupuesto->id.'importes.'.$index), ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                                        <td><a href="#" class="btn btn-danger removePresupuesto"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td>{{Form::date('presupuesto'.$tipoPresupuesto->id.'fechas[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                                    {{-- Si el tipo de presupuesto es 2, mostramos los select anidados --}}
                                                                    @if($tipoPresupuesto->id == 2)
                                                                        <td>
                                                                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px;">
                                                                                {{ Form::select('presupuesto'.$tipoPresupuesto->id.'conceptos[]',
                                                                                    [''=>'','Viaticos'=>'Viáticos','Pasajes'=>'Pasajes','Inscripcion'=>'Inscripción','Otros'=>'Otros'],
                                                                                    '',
                                                                                    ['class' => 'form-control', 'style' => 'width: 120px', 'onchange' => 'seleccionarConcepto(this)']
                                                                                ) }}

                                                                                {{-- Contenedor para campos adicionales --}}
                                                                                <div class="extra-fields" style="display: flex; gap: 10px; align-items: center;">
                                                                                    {{ Form::text('presupuesto'.$tipoPresupuesto->id.'dias[]', '',
                                                                                        ['class' => 'form-control ds_dias', 'placeholder' => 'Días', 'style' => 'display:none; width:150px'])
                                                                                    }}

                                                                                    {{ Form::text('presupuesto'.$tipoPresupuesto->id.'lugar[]', '',
                                                                                        ['class' => 'form-control ds_lugar', 'placeholder' => 'Lugar', 'style' => 'display:none; width:150px'])
                                                                                    }}

                                                                                    {{ Form::select('presupuesto'.$tipoPresupuesto->id.'pasajes[]',
                                                                                        ['' => '', 'Aereo' => 'Aéreo', 'Omnibus' => 'Omnibus', 'Automovil' => 'Automóvil'],
                                                                                        '',
                                                                                        ['class' => 'form-control ds_pasajes', 'style' => 'display:none; width:120px']
                                                                                    ) }}

                                                                                    {{ Form::text('presupuesto'.$tipoPresupuesto->id.'destino[]', '',
                                                                                        ['class' => 'form-control ds_destino', 'placeholder' => 'Destino', 'style' => 'display:none; width:150px'])
                                                                                    }}

                                                                                    {{ Form::text('presupuesto'.$tipoPresupuesto->id.'inscripcion[]', '',
                                                                                        ['class' => 'form-control ds_inscripcion', 'placeholder' => 'Descripción', 'style' => 'display:none; width:150px'])
                                                                                    }}

                                                                                    {{ Form::text('presupuesto'.$tipoPresupuesto->id.'otros[]', '',
                                                                                        ['class' => 'form-control ds_otros', 'placeholder' => 'Otros', 'style' => 'display:none; width:150px'])
                                                                                    }}
                                                                                </div>
                                                                            </div>
                                                                        </td>

                                                                    @else
                                                                        <td>{{ Form::text('presupuesto'.$tipoPresupuesto->id.'detalles[]','',['class' => 'form-control', 'style' => 'width: 400px']) }}</td>
                                                                    @endif
                                                                    <td>{{Form::number('presupuesto'.$tipoPresupuesto->id.'importes[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                                    <td><a href="#" class="btn btn-danger removePresupuesto"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                                </tr>
                                                            @endif




                                                            </tbody>
                                                            <tfoot>
                                                            <tr>
                                                                <td colspan="2" style="text-align: right;">Subtotal:</td>
                                                                <td><input type="text" class="form-control subtotalPresupuesto" readonly style="width:150px;"></td>
                                                            </tr>
                                                            </tfoot>



                                                        </table>
                                                    </div>
                                                </div>

                                            </fieldset>
                                        @endforeach
                                        <div class="form-group" style="display: flex; align-items: center;">
                                            <label for="totalGeneral">Total:</label>
                                            <input type="text" id="totalGeneral" class="form-control" readonly style="width: 150px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                    <a href='{{ route('jovens.index') }}' class="btn btn-warning">Volver</a>
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
        $(document).ready(function () {
            $('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
            $('.js-example-basic-single').select2();

            updateTotals();
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

            // Limpiar el estado del radio button por defecto
            $('input[name="actual"]').prop('checked', false);

            // Seleccionar el radio button por defecto
            $('#actual_1').prop('checked', true);

            // Limpiar el estado del radio button por defecto
            $('input[name="catactual"]').prop('checked', false);

            // Seleccionar el radio button por defecto
            $('#catactual_1').prop('checked', true);

            // Limpiar el estado del radio button por defecto
            $('input[name="sicadiactual"]').prop('checked', false);

            // Seleccionar el radio button por defecto
            $('#sicadiactual_1').prop('checked', true);



        });


        $('.addRowBeca').on('click',function(e){
            e.preventDefault();
            addRowBeca();
        });
        function addRowBeca()
        {
            var tr='<tr>'+
                '<td>'+'{{ Form::select('institucions[]',[''=>'','ANPCyT'=>'ANPCyT','CIC'=>'CIC','CONICET'=>'CONICET','UNLP'=>'UNLP','CIN'=>'CIN','OTRA'=>'OTRA'], '',['class' => 'form-control institucion_select', 'style' => 'width: 150px']) }}'+'</td>'+
                '<td>'+'{{ Form::select('becas[]',[''], '',['class' => 'form-control beca_select', 'style' => 'width: 150px']) }}'+'</td>'+
                '<td>'+'{{Form::date('becadesdes[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}'+'</td>'+
                '<td>'+'{{Form::date('becahastas[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}'+'{{Form::hidden('becaagregadas[]', 1)}}'+'</td>'+


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

        $(document).on('change', 'select.institucionActual_select', function() {
            var rowIndex = $(this).closest('tr').index(); // Obtener el índice de la fila actual
            var institucionSeleccionada = $(this).val(); // Obtener la institución seleccionada

            // Obtener el select de becas en la misma fila
            var becasSelect = $('tbody#cuerpoBecaActual tr:eq(' + rowIndex + ') select.becaActual_select');
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

        // Al cambiar cualquier select de instituciones
        $(document).on('change', 'select.institucion_select', function() {
            var rowIndex = $(this).closest('tr').index(); // Obtener el índice de la fila actual
            var institucionSeleccionada = $(this).val(); // Obtener la institución seleccionada

            // Obtener el select de becas en la misma fila
            var becasSelect = $('tbody#cuerpoBecas tr:eq(' + rowIndex + ') select.beca_select');
            var opciones = obtenerOpcionesBecaPorInstitucionAnteriores(institucionSeleccionada); // Obtener opciones de beca

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
        function obtenerOpcionesBecaPorInstitucionAnteriores(institucionSeleccionada) {
            //console.log(institucionSeleccionada)
            var opciones = @json(config('becasAnteriores'));
            if (opciones[institucionSeleccionada]) {
                return opciones[institucionSeleccionada];
            }
            return ['']; // Opción por defecto
        }

        function obtenerOpcionesBecaPorInstitucion(institucionSeleccionada) {
            //console.log(institucionSeleccionada)
            var opciones = @json(config('becas'));
            if (opciones[institucionSeleccionada]) {
                return opciones[institucionSeleccionada];
            }
            return ['']; // Opción por defecto
        }


        $('.addRowProyecto').on('click',function(e){
            e.preventDefault();
            addRowProyecto();
        });
        function addRowProyecto()
        {



            var tr='<tr>'+
                '<td>'+'{{Form::text('codigoAnterior[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}'+'</td>'+
                '<td>'+'{{Form::textarea('tituloAnterior[]', '', ['class' => 'form-control', 'style' => 'width:450px;','rows'=>2])}}'+'</td>'+
                '<td>'+'{{Form::text('directorAnterior[]', '', ['class' => 'form-control', 'style' => 'width:200px;'])}}'+'</td>'+
                '<td>'+'{{Form::date('inicioAnterior[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}'+'</td>'+
                '<td>'+'{{Form::date('finAnterior[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}'+'</td>'+

                '<td><a href="#" class="btn btn-danger removeProyecto"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoProyectos').append(tr);



        };

        $('body').on('click', '.removeProyecto', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
            }


        });


        $('.addRowPresupuesto').on('click',function(e){
            e.preventDefault();
            var tipoId = $(this).data('tipo-id'); // Obtener el tipo de presupuesto
            //console.log(tipoId);
            addRowPresupuesto(tipoId);
            // Llama a updateTotals tras agregar una fila
            updateTotals();
        });
        function addRowPresupuesto(tipoId)
        {
            var tr = '';

            // Diferente lógica para tipoId 2
            if (tipoId == 2) {
                tr = '<tr>' +

                    '<td><input type="date" name="presupuesto' + tipoId + 'fechas[]" class="form-control" style="width: 150px;"></td>' +

                    '<td>' +
                    '<div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">' +

                    '<select name="presupuesto' + tipoId + 'conceptos[]" class="form-control" onchange="seleccionarConcepto(this)" style="width:120px;">' +
                    '<option value="">-- seleccionar --</option>' +
                    '<option value="Viaticos">Viáticos</option>' +
                    '<option value="Pasajes">Pasajes</option>' +
                    '<option value="Inscripcion">Inscripción</option>' +
                    '<option value="Otros">Otros</option>' +
                    '</select>' +

                    '<div class="extra-fields" style="display:flex; gap:10px;">' +

                    // 👇 TODOS LOS CAMPOS SIEMPRE EXISTEN
                    '<input type="hidden" name="presupuesto' + tipoId + 'dias[]" value="">' +
                    '<input type="hidden" name="presupuesto' + tipoId + 'lugar[]" value="">' +
                    '<input type="hidden" name="presupuesto' + tipoId + 'pasajes[]" value="">' +
                    '<input type="hidden" name="presupuesto' + tipoId + 'destino[]" value="">' +
                    '<input type="hidden" name="presupuesto' + tipoId + 'inscripcion[]" value="">' +
                    '<input type="hidden" name="presupuesto' + tipoId + 'otros[]" value="">' +

                    // inputs visibles (se pisan los hidden)
                    '<input type="text" class="form-control ds_dias" placeholder="Días" style="display:none; width:150px;">' +
                    '<input type="text" class="form-control ds_lugar" placeholder="Lugar" style="display:none; width:150px;">' +

                    '<select class="form-control ds_pasajes" style="display:none; width:150px;">' +
                    '<option value="">-- seleccionar --</option>' +
                    '<option value="Aereo">Aéreo</option>' +
                    '<option value="Omnibus">Omnibus</option>' +
                    '<option value="Automovil">Automóvil</option>' +
                    '</select>' +

                    '<input type="text" class="form-control ds_destino" placeholder="Destino" style="display:none; width:150px;">' +
                    '<input type="text" class="form-control ds_inscripcion" placeholder="Descripción" style="display:none; width:150px;">' +
                    '<input type="text" class="form-control ds_otros" placeholder="Otros" style="display:none; width:150px;">' +

                    '</div></div></td>' +

                    '<td><input type="number" name="presupuesto' + tipoId + 'importes[]" class="form-control" style="width:150px;"></td>' +

                    '<td><a href="#" class="btn btn-danger removePresupuesto"><i class="glyphicon glyphicon-remove"></i></a></td>' +
                    '</tr>';
            }
            else {
                // Lógica por defecto para otros tipos
                tr = '<tr>' +
                    '<td><input type="date" name="presupuesto' + tipoId + 'fechas[]" class="form-control" style="width: 150px;"></td>' +
                    '<td><input type="text" name="presupuesto' + tipoId + 'detalles[]" class="form-control" style="width: 400px;"></td>' +
                    '<td><input type="number" name="presupuesto' + tipoId + 'importes[]" class="form-control" style="width: 150px;"></td>' +
                    '<td><a href="#" class="btn btn-danger removePresupuesto"><i class="glyphicon glyphicon-remove"></i></a></td>' +
                    '</tr>';
            }

            // Agregar la fila al tbody correspondiente
            $('.cuerpoPresupuesto[data-tipo-id="' + tipoId + '"]').append(tr);



        };

        $('body').on('click', '.removePresupuesto', function(e){

            e.preventDefault();
            var confirmDelete = confirm('¿Estás seguro?');

            if (confirmDelete) {
                $(this).parent().parent().remove();
                // Llama a updateTotals tras eliminar una fila
                updateTotals();

            }

        });


        // Función para mostrar/ocultar campos según el concepto seleccionado
        function seleccionarConcepto(select) {
            var value = select.value;
            var row = $(select).closest('tr');

            // Ocultar todos los campos primero
            row.find('.ds_dias, .ds_lugar, .ds_pasajes, .ds_destino, .ds_inscripcion, .ds_otros').hide();

            // Mostrar los campos en función del valor seleccionado
            if (value === 'Viaticos') {
                row.find('.ds_dias, .ds_lugar').show();
            } else if (value === 'Pasajes') {
                row.find('.ds_pasajes, .ds_destino').show();
            } else if (value === 'Inscripcion') {
                row.find('.ds_inscripcion').show();
            } else if (value === 'Otros') {
                row.find('.ds_otros').show();
            }
        }

        // Actualiza el subtotal y total general
        function updateTotals() {
            var totalGeneral = 0;

            // Para cada tipo de presupuesto, calcula su subtotal
            $('.cuerpoPresupuesto').each(function() {
                var subtotal = 0;
                $(this).find('input[name^="presupuesto"][name$="importes[]"]').each(function() {
                    var importe = parseFloat($(this).val()) || 0;
                    subtotal += importe;
                });

                // Muestra el subtotal en el campo correspondiente
                $(this).closest('table').find('.subtotalPresupuesto').val(subtotal.toFixed(2));

                // Acumula el subtotal en el total general
                totalGeneral += subtotal;
            });

            // Muestra el total general
            $('#totalGeneral').val(totalGeneral.toFixed(2));
        }

        // Llama a updateTotals cuando cambien los valores de "Importe"
        $('body').on('input', 'input[name^="presupuesto"][name$="importes[]"]', function() {
            updateTotals();
        });



    </script>

@endsection
