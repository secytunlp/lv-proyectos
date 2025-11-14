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
                <i class="fa fa-user-friends" aria-hidden="true"></i>Integrante
                <small>Cambio de Tipo de Integrante</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('integrantes.index') }}">Integrantes</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">@if($proyecto) {{ $proyecto->codigo }} {{ $proyecto->titulo }}@endif</h3>
                        </div>


                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('integrantes.cambiarTipo',$integrante->id) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="box-body">
                                @include('includes.messages')
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#datos_proyecto" aria-controls="datos_proyecto" role="tab" data-toggle="tab">Proyecto</a></li>
                                    <li role="presentation"><a href="#datos_personales" aria-controls="datos_personales" role="tab" data-toggle="tab">Datos Personales</a></li>
                                    <li role="presentation"><a href="#universidad" aria-controls="universidad" role="tab" data-toggle="tab">Universidad</a></li>
                                    <li role="presentation"><a href="#investigacion" aria-controls="investigacion" role="tab" data-toggle="tab">Investigación</a></li>
                                    <li role="presentation"><a href="#categorizacion" aria-controls="categorizacion" role="tab" data-toggle="tab">Categorización</a></li>
                                    <li role="presentation"><a href="#becario" aria-controls="becario" role="tab" data-toggle="tab">Becas</a></li>
                                    <!-- Agrega más pestañas según sea necesario -->
                                </ul>
                                <div class="tab-content" style="margin: 1%;">
                                    <div role="tabpanel" class="tab-pane active" id="datos_proyecto">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="hidden" name="proyecto_id" value="{{ $proyecto->id ?? '' }}">
                                                    <input type="hidden" name="alta" value="{{ ($integrante->alta)?date('Y-m-d', strtotime($integrante->alta)):'' }}">
                                                    <input type="hidden" name="cambio" value="{{ ($integrante->cambio)?date('Y-m-d', strtotime($integrante->cambio)):'' }}">
                                                    <input type="hidden" name="horas_anteriores" value="{{ ($integrante->horas_anteriores)?$integrante->horas_anteriores:$integrante->horas }}">
                                                    {{Form::label('tipo', 'Tipo')}}
                                                    {{ Form::select('tipo',$filteredTipos, '',['class' => 'form-control']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('horas', 'Horas')}}
                                                    {{Form::number('horas', $integrante->horas, ['class' => 'form-control','placeholder'=>'Horas'])}}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-2">

                                                <div class="form-group">
                                                    {{Form::label('cambio', 'Fecha de cambio')}}
                                                    {{Form::date('cambio', ($integrante->cambio)?date('Y-m-d', strtotime($integrante->cambio)):'', ['class' => 'form-control'])}}

                                                </div>
                                            </div>
                                            <div class="col-md-6" id="divReducccion">

                                                <div class="form-group">
                                                    {{Form::label('reduccion', 'En el caso de ser una reducción horaria, especificar las consecuencias que la misma tendrá en el desarrollo del proyecto')}}
                                                    {{Form::textarea('reduccion', $integrante->reduccion, ['class' => 'form-control'])}}

                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="datos_personales">

                                        <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{Form::label('apellido', 'Apellido')}}
                                                {{Form::text('apellido', $integrante->investigador->persona->apellido, ['class' => 'form-control','placeholder'=>'Apellido','disabled'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{Form::label('nombre', 'Nombre')}}
                                                {{Form::text('nombre', $integrante->investigador->persona->nombre, ['class' => 'form-control','placeholder'=>'Nombre','disabled'])}}
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('cuil', 'CUIL')}}
                                                {{Form::text('cuil', $integrante->investigador->persona->cuil, ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X','disabled'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{Form::label('email', 'Email')}}
                                                {{Form::email('email', $integrante->email, ['class' => 'form-control','placeholder'=>'Email'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('nacimiento', 'Nacimiento')}}
                                                {{Form::date('nacimiento', ($integrante->nacimiento)?date('Y-m-d', strtotime($integrante->nacimiento)):'', ['class' => 'form-control'])}}
                                            </div>
                                        </div>
                                    </div>

                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="universidad">
                                        <div class="row">

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('facultad', 'U. Académica')}}
                                                    {{Form::select('facultad_id',  $facultades,$integrante->facultad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'facultad_id'])}}

                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('universidad', 'Universidad')}}
                                                    {{Form::select('universidad_id',  $universidades,$integrante->universidad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'universidad_id'])}}

                                                </div>
                                            </div>


                                        </div>
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

                                                    <td>{{ Form::select('titulos[]',$titulos, $integrante->titulo_id,['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px']) }}</td>
                                                    <td>{{Form::date('egresos[]', ($integrante->egresogrado)?date('Y-m-d', strtotime($integrante->egresogrado)):'', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>

                                                    <!--<td><a href="#" class="btn btn-danger remove"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                </tr>

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
                                                    {{Form::text('carrera', $integrante->carrera, ['class' => 'form-control','placeholder'=>'Carrera'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('total', 'Total De Materias')}}
                                                    {{Form::number('total', $integrante->total, ['class' => 'form-control','placeholder'=>'Total De Materias'])}}

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('materias', 'Aprobadas')}}
                                                    {{Form::number('materias', $integrante->materias, ['class' => 'form-control','placeholder'=>'Aprobadas'])}}
                                                </div>
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
                                                    <!--<th><a href="#" class="addRowPost"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoPosgrado">
                                                    <tr>

                                                        <td>{{ Form::select('tituloposts[]',$tituloposts, $integrante->titulopost_id,['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px']) }}</td>
                                                        <td>{{Form::date('egresoposts[]', ($integrante->egresoposgrado)?date('Y-m-d', strtotime($integrante->egresoposgrado)):'', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>

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
                                                    <!--<th>U. Académica</th>
                                                    <th>Universidad</th>-->
                                                    <!--<th>Activo</th>
                                                    <th><a href="#" class="addRowCargo"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoCargos">
                                                    <tr>

                                                        <td>{{ Form::select('cargos[]',$cargos, $integrante->cargo_id,['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                        <td>{{ Form::select('deddocs[]',[''=>'','Exclusiva'=>'Exclusiva','Semi Exclusiva'=>'Semi Exclusiva','Simple'=>'Simple'], $integrante->deddoc,['class' => 'form-control', 'style' => 'width: 120px']) }}</td>
                                                        <td>{{Form::date('ingresos[]', ($integrante->alta_cargo)?date('Y-m-d', strtotime($integrante->alta_cargo)):'', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                        <!--<td>{{ Form::select('facultads[]',$facultades, $integrante->facultad_id,['class' => 'form-control', 'style' => 'width: 300px']) }}</td>
                                                        <td>{{ Form::select('universidads[]',$universidades, $integrante->universidad_id,['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px']) }}</td>-->
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
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    {{Form::label('unidad', 'Lugar de Trabajo')}}
                                                    {{Form::select('unidad_id',  $unidads,$integrante->unidad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidad_id'])}}

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

                                                    <!--<th>Actual</th>
                                                    <th><a href="#" class="addRowCarrerainv"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoCarrerainvs">
                                                    <tr>

                                                        <td>{{ Form::select('carrerainvs[]',$carrerainvs, $integrante->carrerainv_id,['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                        <td>{{ Form::select('organismos[]',$organismos, $integrante->organismo_id,['class' => 'form-control', 'style' => 'width: 150px']) }}</td>
                                                        <td>{{Form::date('carringresos[]', ($integrante->ingreso_carrera)?date('Y-m-d', strtotime($integrante->ingreso_carrera)):'', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>


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

                                                        <td>{{ Form::select('categorias[]',$categorias, $integrante->categoria_id,['class' => 'form-control', 'style' => 'width: 60px']) }}</td>
                                                        <!--<td>{{ Form::select('catyears[]',$years, '',['class' => 'form-control', 'style' => 'width: 60px']) }}</td>
                                                        <td>{{Form::date('catnotificacions[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                        <td>{{ Form::select('catuniversidads[]',$universidades, '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px']) }}</td>

                                                        <td>{{ Form::radio('catactual', 1, true,['id' => 'catactual_1']) }}</td>
                                                        <td><a href="#" class="btn btn-danger removeCategoria"><i class="glyphicon glyphicon-remove"></i></a></td>-->
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

                                                        <td>{{ Form::select('sicadis[]',$sicadis, $integrante->sicadi_id,['class' => 'form-control', 'style' => 'width: 120px']) }}</td>
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
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Beca</legend>

                                            <div class="form-group col-md-12">

                                                <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>


                                                    <th>Institución</th>
                                                    <th>Beca</th>
                                                    <th>Desde</th>
                                                    <th>Hasta</th>
                                                    <!--<th>UNLP</th>
                                                    <th><a href="#" class="addRowBeca"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoBecas">
                                                    <tr>

                                                        <td>{{ Form::select('institucions[]',[''=>'','ANPCyT'=>'ANPCyT','CIC'=>'CIC','CONICET'=>'CONICET','UNLP'=>'UNLP','CIN'=>'CIN','OTRA'=>'OTRA'], $integrante->institucion,['class' => 'form-control institucion_select', 'style' => 'width: 150px']) }}</td>
                                                        <td>{{ Form::select('becas[]',\App\Helpers\BecaHelper::obtenerOpcionesBecaPorInstitucion(old('institucions.0', $integrante->institucion)), $integrante->beca,['class' => 'form-control beca_select', 'style' => 'width: 150px']) }}</td>

                                                        <td>{{Form::date('becadesdes[]', ($integrante->alta_beca)?date('Y-m-d', strtotime($integrante->alta_beca)):'', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>

                                                        <td>{{Form::date('becahastas[]',  ($integrante->baja_beca)?date('Y-m-d', strtotime($integrante->baja_beca)):'', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                        <!--<td>{{Form::checkbox('becaunlps[]', 1,false)}}</td>
                                                        <td><a href="#" class="btn btn-danger removeCategoria"><i class="glyphicon glyphicon-remove"></i></a></td>-->
                                                    </tr>

                                                    </tbody>




                                                </table>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="resolucion">Resolución Beca ó Certificado de alumno de Doctorado/Maestría</label>
                                                    <input type="file" name="resolucion" class="form-control" placeholder="">
                                                    @if(!empty($integrante->resolucion))
                                                        <a href="{{ url($integrante->resolucion) }}" target="_blank">Descargar Resolución</a>
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" name="delete_resolucion" value="1">
                                                                Eliminar Resolución actual
                                                            </label>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href="{{ route('integrantes.index') }}?proyecto_id={{ $proyecto->id }}" class="btn btn-warning">Volver</a>

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
            @if($integrante->titulo_id)
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
        function toggleReduccionDiv() {
            var horas = parseInt($('input[name="horas"]').val());
            var horas_anteriores = parseInt($('input[name="horas_anteriores"]').val());

            if (horas < horas_anteriores) {
                $('#divReducccion').show();
            } else {
                $('#divReducccion').hide();
            }
        }

        // Inicialmente, oculta el div si horas no es menor a horas_anteriores
        toggleReduccionDiv();

        // Agrega el evento change al input de horas
        $('input[name="horas"]').on('change keyup', function() {
            toggleReduccionDiv();
        });
    </script>

@endsection
