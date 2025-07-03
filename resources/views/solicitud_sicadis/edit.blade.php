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
                <i class="fa fa-layer-group" aria-hidden="true"></i>Solicitud
                <small>Editar</small>
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
                        <div class="box-header with-border">
                            <h3 class="box-title">Editar</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('solicitud_sicadis.update',$investigador->id) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="box-body">
                                @include('includes.messages')
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#datos_personales" aria-controls="datos_personales" role="tab" data-toggle="tab">Datos Personales</a></li>
                                    <li role="presentation"><a href="#universidad" aria-controls="universidad" role="tab" data-toggle="tab">Datos Académicos</a></li>
                                    <li role="presentation"><a href="#investigacion" aria-controls="investigacion" role="tab" data-toggle="tab">Cargo en Investigación</a></li>
                                    <li role="presentation"><a href="#becario" aria-controls="becario" role="tab" data-toggle="tab">Beca</a></li>
                                    <li role="presentation"><a href="#proyecto" aria-controls="proyecto" role="tab" data-toggle="tab">Proyecto</a></li>
                                    <li role="presentation"><a href="#categorizacion" aria-controls="categorizacion" role="tab" data-toggle="tab">Categorización</a></li>

                                    <!-- Agrega más pestañas según sea necesario -->
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="datos_personales">

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('apellido', 'Apellido')}}
                                                    {{Form::text('apellido', $investigador->apellido, ['class' => 'form-control','placeholder'=>'Apellido'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('nombre', 'Nombre')}}
                                                    {{Form::text('nombre', $investigador->nombre, ['class' => 'form-control','placeholder'=>'Nombre'])}}
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('cuil', 'CUIL')}}
                                                    {{Form::text('cuil', $investigador->cuil, ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('genero', 'Género')}}
                                                    {{ Form::select('genero',['' => 'Seleccionar...'] + config('generos'), $investigador->genero,['class' => 'form-control','id'=>'genero']) }}

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('nacimiento', 'Nacimiento')}}
                                                    {{Form::date('nacimiento', ($investigador->nacimiento)?date('Y-m-d', strtotime($investigador->nacimiento)):'', ['class' => 'form-control'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('nacionalidad', 'Nacionalidad')}}
                                                    {{Form::text('nacionalidad', $investigador->nacionalidad, ['class' => 'form-control','placeholder'=>'Nacionalidad'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('calle', 'Calle')}}
                                                    {{Form::text('calle', $investigador->calle, ['class' => 'form-control','placeholder'=>'Calle'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('nro', 'Número')}}
                                                    {{Form::text('nro', $investigador->nro, ['class' => 'form-control','placeholder'=>'Número'])}}

                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('piso', 'Piso')}}
                                                    {{Form::text('piso', $investigador->piso, ['class' => 'form-control','placeholder'=>'Piso'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('dpto', 'Depto.')}}
                                                    {{Form::text('dpto', $investigador->dpto, ['class' => 'form-control','placeholder'=>'Depto.'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('cp', 'CP')}}
                                                    {{Form::text('cp', $investigador->cp, ['class' => 'form-control','placeholder'=>'CP'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('celular', 'Celular')}}
                                                    {{Form::text('celular', $investigador->celular, ['class' => 'form-control','placeholder'=>'Celular'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('email_institucional', 'Email institucional')}}
                                                    {{Form::text('email_institucional', $investigador->email_institucional, ['class' => 'form-control','placeholder'=>'Email institucional'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('email_alternativo', 'Email alternativo')}}
                                                    {{Form::text('email_alternativo', $investigador->email_alternativo, ['class' => 'form-control','placeholder'=>'Email alternativo'])}}
                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    {{Form::label('notificacion', 'Acepto recibir toda notificación relativa a la presente solicitud en la
dirección de correo electrónico institucional declarada precedentemente')}}
                                                    {{Form::checkbox('notificacion', 1,($investigador->notificacion)?true:false)}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('sedici', 'Perfil SEDICI')}}
                                                    {{Form::text('sedici', $investigador->sedici, ['class' => 'form-control','placeholder'=>'Perfil SEDICI'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('orcid', 'Número ORCID')}}
                                                    {{Form::text('orcid', $investigador->orcid, ['class' => 'form-control','placeholder'=>'Número ORCID'])}}
                                                </div>
                                            </div>


                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {{Form::label('scholar', 'Perfil de Google Académico')}}
                                                    {{Form::text('scholar', $investigador->scholar, ['class' => 'form-control','placeholder'=>'Perfil de Google Académico'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('scopus', 'Número SCOPUS')}}
                                                    {{Form::text('scopus', $investigador->scopus, ['class' => 'form-control','placeholder'=>'Número SCOPUS'])}}
                                                </div>
                                            </div>


                                        </div>


                                        <div class="row">
                                            <div class="col-md-4">

                                                <div class="form-group">
                                                    <label for="foto">Foto</label>
                                                    @if($investigador->foto)
                                                        <img id="original" src="{{ asset($investigador->foto) }}" height="200">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" name="delete_image" value="1">
                                                                Eliminar foto actual
                                                            </label>
                                                        </div>
                                                    @endif
                                                    <input type="file" name="foto" class="form-control" placeholder="">

                                                </div>
                                            </div>
                                            @can('administrador-general')
                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    {{Form::label('observaciones', 'Observaciones')}}
                                                    {{Form::textarea('observaciones', $investigador->observaciones, ['class' => 'form-control'])}}

                                                </div>
                                            </div>
                                            @endcan
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="universidad">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('titulo', 'Título de grado')}}
                                                    {{Form::text('titulo', $investigador->titulo, ['class' => 'form-control','placeholder'=>'Título de grado'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('titulo_entidad', 'Entidad otorgante')}}
                                                    {{Form::text('titulo_entidad', $investigador->titulo_entidad, ['class' => 'form-control','placeholder'=>'Entidad otorgante'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('tipo_posgrado', 'Grado Académico')}}
                                                    {{ Form::select('tipo_posgrado',['' => 'Seleccionar...'] + config('tipoPosgrados'), $investigador->tipo_posgrado,['class' => 'form-control','id'=>'Tipo']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('posgrado', 'Título de posgrado')}}
                                                    {{Form::text('posgrado', $investigador->posgrado, ['class' => 'form-control','placeholder'=>'Título de posgrado'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('posgrado_entidad', 'Entidad otorgante')}}
                                                    {{Form::text('posgrado_entidad', $investigador->posgrado_entidad, ['class' => 'form-control','placeholder'=>'Entidad otorgante'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="actividades" style="color: #a90062">Indicar máximo grado académico alcanzado (postgrado)</label>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('cargo_docente', 'Cargo docente')}}
                                                    {{ Form::select('cargo_docente',['' => 'Seleccionar...'] + config('cargos'), $investigador->cargo_docente,['class' => 'form-control','id'=>'cargo_docente']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('cargo_dedicacion', 'Dedicación')}}
                                                    {{ Form::select('cargo_dedicacion',['' => 'Seleccionar...'] + config('dedicaciones'), $investigador->cargo_dedicacion,['class' => 'form-control','id'=>'cargo_dedicacion']) }}

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('cargo_ua', 'U. Académica')}}

                                                    {{ Form::select('cargo_ua',['' => 'Seleccionar...'] + config('facultades'), $investigador->cargo_ua,['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'cargo_ua']) }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    {{Form::label('investigacion_ui', 'Lugar de trabajo')}}

                                                    <select id="investigacion_ui" class="form-control js-example-basic-single" name="investigacion_ui" style="width: 100%">
                                                        <option value="">Seleccionar</option>
                                                        @foreach($instituciones as $clave => $valor)
                                                                <?php $optionValue = $clave . ' - ' . $valor; ?>
                                                            <option value="{{ $optionValue }}"
                                                                {{ ($optionValue == ($investigador->ui_sigla . ' - ' . $investigador->ui_nombre)) ? 'selected' : '' }}>
                                                                {{ $optionValue }}
                                                            </option>
                                                        @endforeach
                                                    </select>




                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="investigacion">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span style="font-weight: bold;font-style: italic;">Completar solo si se está desempeñando
actualmente como Investigadora/or o CPA</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('carrera_cargo', 'Cargo')}}
                                                    {{ Form::select('carrera_cargo',['' => 'Seleccionar...'] + config('carreraCargos'), $investigador->carrera_cargo,['class' => 'form-control','id'=>'carrera_cargo']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('carrera_empleador', 'Empleador')}}

                                                    {{ Form::select('carrera_empleador',['' => 'Seleccionar...'] + config('carreraEmpleadores'), $investigador->carrera_empleador,['class' => 'form-control','id'=>'carrera_empleador']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('carrera_ingreso', 'Ingreso')}}
                                                    {{Form::date('carrera_ingreso', ($investigador->carrera_ingreso)?date('Y-m-d', strtotime($investigador->carrera_ingreso)):'', ['class' => 'form-control'])}}
                                                </div>
                                            </div>


                                        </div>



                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    {{Form::label('carrera_ui', 'Lugar de trabajo')}}

                                                    <select id="carrera_ui" class="form-control js-example-basic-single" name="carrera_ui" style="width: 100%">
                                                        <option value="">Seleccionar</option>
                                                        @foreach($instituciones as $clave => $valor)
                                                                <?php $optionValue = $clave . ' - ' . $valor; ?>
                                                            <option value="{{ $optionValue }}"
                                                                {{ ($optionValue == ($investigador->carrera_ui_sigla . ' - ' . $investigador->carrera_ui_nombre)) ? 'selected' : '' }}>
                                                                {{ $optionValue }}
                                                            </option>
                                                        @endforeach
                                                    </select>




                                                </div>


                                            </div>
                                        </div>

                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="categorizacion">
                                        <div class="row">

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('convocatoria_id', 'Convocatoria')}}

                                                    {{ Form::select('convocatoria_id',$convocatorias,$investigador->convocatoria_id,['class' => 'form-control','id'=>'convocatoria_id']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-3" id="divMecanismo">
                                                <div class="form-group">
                                                    {{Form::label('mecanismo', 'Mecanismo')}}

                                                    {{ Form::select('mecanismo',['' => 'Seleccionar...'] + config('mecanismos'), $investigador->mecanismo,['class' => 'form-control','id'=>'mecanismo']) }}


                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('presentacion_ua', 'U. Académica')}}

                                                    {{ Form::select('presentacion_ua',['' => 'Seleccionar...'] + config('facultades'), $investigador->presentacion_ua,['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'presentacion_ua']) }}
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('categoria_spu', 'Categoría SPU')}}

                                                    {{ Form::select('categoria_spu',['' => 'Seleccionar...'] + config('spuCategorias'), $investigador->categoria_spu,['class' => 'form-control','id'=>'categoria_spu']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('categoria_solicitada', 'Categoría Solicitada')}}

                                                    {{ Form::select('categoria_solicitada',['' => 'Seleccionar...'] + config('categorias'), $investigador->categoria_solicitada,['class' => 'form-control','id'=>'categoria_solicitada']) }}

                                                </div>
                                            </div>
                                            @can('administrador-general')
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('categoria_asignada', 'Categoría Asignada')}}

                                                    {{ Form::select('categoria_asignada',['' => 'Seleccionar...'] + config('categorias'), $investigador->categoria_asignada,['class' => 'form-control','id'=>'categoria_asignada']) }}
                                                </div>
                                            </div>
                                            @endcan
                                        </div>

                                        <div class="row">

                                            <x-select-areas
                                                :areas="$areas"
                                                :subareas="$subareas"
                                                :selected-area="$selectedArea"
                                                :selected-subarea="$selectedSubarea"
                                            />

                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <label for="actividades" style="color: #a90062">En caso de optar por la categorización por Evaluación, su presentación será evaluada por un Comité Evaluador según la subárea indicada.</label>


                                                </div>
                                            </div>

                                        </div>
                                        @if (session('selected_rol') == 2)
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="curriculum">Curriculum</label>
                                                        <input type="file" name="curriculum" class="form-control" placeholder="">
                                                        @if(!empty($investigador->curriculum))
                                                            <a href="{{ asset($investigador->curriculum) }}" target="_blank">Descargar Curriculum</a>
                                                            <div class="checkbox">
                                                                <label>
                                                                    <input type="checkbox" name="delete_cv" value="1">
                                                                    Eliminar Curriculum actual
                                                                </label>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                        <label for="actividades" style="color: #a90062">IMPORTANTE: El CV deberá ser el generado (pdf/doc) por el sistema SIGEVA-UNLP (banco de datos de
                                                            actividades de ciencia y técnica)</label>


                                                    </div>
                                                </div>

                                            </div>
                                        @endif
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="becario">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('beca_tipo', 'Tipo')}}
                                                    {{ Form::select('beca_tipo',['' => 'Seleccionar...'] + config('becaTipos'), $investigador->beca_tipo,['class' => 'form-control','id'=>'beca_tipo']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('beca_entidad', 'Entidad otorgante')}}
                                                    {{ Form::select('beca_entidad',['' => 'Seleccionar...'] + config('becaEntidades'), $investigador->beca_entidad,['class' => 'form-control','id'=>'beca_entidad']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('beca_inicio', 'Inicio')}}
                                                    {{Form::date('beca_inicio', ($investigador->beca_inicio)?date('Y-m-d', strtotime($investigador->beca_inicio)):'', ['class' => 'form-control'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('beca_fin', 'Fin')}}
                                                    {{Form::date('beca_fin', ($investigador->beca_fin)?date('Y-m-d', strtotime($investigador->beca_fin)):'', ['class' => 'form-control'])}}
                                                </div>
                                            </div>

                                        </div>



                                        <div class="row">
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    {{Form::label('beca_ui', 'Lugar de trabajo')}}

                                                    <select id="beca_ui" class="form-control js-example-basic-single" name="beca_ui" style="width: 100%">
                                                        <option value="">Seleccionar</option>
                                                        @foreach($instituciones as $clave => $valor)
                                                                <?php $optionValue = $clave . ' - ' . $valor; ?>
                                                            <option value="{{ $optionValue }}"
                                                                {{ ($optionValue == ($investigador->beca_ui_sigla . ' - ' . $investigador->beca_ui_nombre)) ? 'selected' : '' }}>
                                                                {{ $optionValue }}
                                                            </option>
                                                        @endforeach
                                                    </select>




                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="proyecto">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <span style="font-weight: bold;font-style: italic;">Indique los datos del proyecto de Investigación UNLP vigente en que participa actualmente.<br>Si no participa de un proyecto UNLP, podrá indicar alternativamente proyectos de entidades reconocidas (CONICET, CIC-PBA, Agencia ID+i, etc) que se ejecuten en la UNLP.
<br>Podrá consultar los proyectos UNLP en los que participa <a href="https://cyt.proyectos.unlp.edu.ar" target="_blank">aquí</a> </span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('proyecto_entidad', 'Entidad')}}

                                                    {{ Form::select('proyecto_entidad',['' => 'Seleccionar...'] + config('proyectoEntidades'), $investigador->proyecto_entidad,['class' => 'form-control','id'=>'proyecto_entidad']) }}

                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('proyecto_codigo', 'Código')}}
                                                    {{Form::text('proyecto_codigo', $investigador->proyecto_codigo, ['class' => 'form-control','placeholder'=>'Código'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('proyecto_director', 'Director')}}
                                                    {{Form::text('proyecto_director', $investigador->proyecto_director, ['class' => 'form-control','placeholder'=>'Director'])}}
                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('proyecto_inicio', 'Inicio')}}
                                                    {{Form::date('proyecto_inicio', ($investigador->proyecto_inicio)?date('Y-m-d', strtotime($investigador->proyecto_inicio)):'', ['class' => 'form-control'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('proyecto_fin', 'Fin')}}
                                                    {{Form::date('proyecto_fin', ($investigador->proyecto_fin)?date('Y-m-d', strtotime($investigador->proyecto_fin)):'', ['class' => 'form-control'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    {{Form::label('proyecto_titulo', 'Título')}}
                                                    {{Form::textarea('proyecto_titulo', $investigador->proyecto_titulo, ['class' => 'form-control'])}}

                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                    <a href='{{ route('solicitud_sicadis.index') }}' class="btn btn-warning">Volver</a>
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

    <script src="{{ asset('dist/js/custom.js') }}"></script>
    <!-- page script -->
    <script>
        $(document).ready(function () {
            //$('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
            $('.js-example-basic-single').select2();

            $("#convocatoria_id").change(function() {
                toggleDivOnSelect('convocatoria_id', 'divMecanismo', 'Equivalencia');
            });
            toggleDivOnSelect('convocatoria_id', 'divMecanismo', 'Equivalencia');

        });


    </script>

@endsection
