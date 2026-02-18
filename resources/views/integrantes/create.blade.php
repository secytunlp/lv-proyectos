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
                <small>Alta de integrante</small>
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
                        <form role="form" action="{{ route('integrantes.store') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
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
                                                    {{Form::label('tipo', 'Tipo')}}
                                                    {{ Form::select('tipo',[''=>'','Investigador Formado'=>'Investigador Formado','Investigador En Formación'=>'Investigador En Formación','Becario, Tesista'=>'Becario, Tesista','Colaborador'=>'Colaborador'], '',['class' => 'form-control']) }}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('horas', 'Horas')}}
                                                    {{Form::number('horas', '', ['class' => 'form-control','placeholder'=>'Horas'])}}
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

                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="datos_personales">

                                        <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{Form::label('apellido', 'Apellido')}}
                                                {{Form::text('apellido', '', ['class' => 'form-control','placeholder'=>'Apellido'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{Form::label('nombre', 'Nombre')}}
                                                {{Form::text('nombre', '', ['class' => 'form-control','placeholder'=>'Nombre'])}}
                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('cuil', 'CUIL')}}
                                                {{Form::text('cuil', '', ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                {{Form::label('email', 'Email')}}
                                                {{Form::email('email', '', ['class' => 'form-control','placeholder'=>'Email'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('nacimiento', 'Nacimiento')}}
                                                {{Form::date('nacimiento', '', ['class' => 'form-control'])}}
                                            </div>
                                        </div>
                                    </div>

                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="universidad">
                                        <div class="row">

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('facultad', 'U. Académica')}}
                                                    {{Form::select('facultad_id',  $facultades,'', ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'facultad_id'])}}

                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('universidad', 'Universidad')}}
                                                    {{Form::select('universidad_id',  $universidades,'', ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'universidad_id'])}}

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

                                                    <td>{{ Form::select('titulos[]',$titulos, '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px']) }}</td>
                                                    <td>{{Form::date('egresos[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>

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

                                                        <td>{{ Form::select('tituloposts[]',$tituloposts, '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px']) }}</td>
                                                        <td>{{Form::date('egresoposts[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>

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

                                                        <td>{{ Form::select('cargos[]',$cargos, '',['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                        <td>{{ Form::select('deddocs[]',[''=>'','Exclusiva'=>'Exclusiva','Semi Exclusiva'=>'Semi Exclusiva','Simple'=>'Simple'], '',['class' => 'form-control', 'style' => 'width: 120px']) }}</td>
                                                        <td>{{Form::date('ingresos[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                        <!--<td>{{ Form::select('facultads[]',$facultades, '',['class' => 'form-control', 'style' => 'width: 300px']) }}</td>
                                                        <td>{{ Form::select('universidads[]',$universidades, '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px']) }}</td>-->
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

                                                    <!--<th>Actual</th>
                                                    <th><a href="#" class="addRowCarrerainv"><i class="glyphicon glyphicon-plus"></i></a></th>-->

                                                    </thead>

                                                    <tbody id="cuerpoCarrerainvs">
                                                    <tr>

                                                        <td>{{ Form::select('carrerainvs[]',$carrerainvs, '',['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                        <td>{{ Form::select('organismos[]',$organismos, '',['class' => 'form-control', 'style' => 'width: 150px']) }}</td>
                                                        <td>{{Form::date('carringresos[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>


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

                                                        <td>{{ Form::select('categorias[]',$categorias, '',['class' => 'form-control', 'style' => 'width: 60px']) }}</td>
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

                                                        <td>{{ Form::select('sicadis[]',$sicadis, '',['class' => 'form-control', 'style' => 'width: 120px']) }}</td>
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
                                                    @php
                                                        $instituciones = config('becaEntidades');
                                                        unset($instituciones['Otra']);
                                                        $instituciones['CIN'] = 'CIN';
                                                    @endphp
                                                    <tr>

                                                        <td>{{ Form::select('institucions[]',[''=>'']+$instituciones, '',['class' => 'form-control institucion_select', 'style' => 'width: 150px']) }}</td>
                                                        <td>{{ Form::select('becas[]',[''=>''], '',['class' => 'form-control beca_select', 'style' => 'width: 150px']) }}</td>

                                                        <td>{{Form::date('becadesdes[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>

                                                        <td>{{Form::date('becahastas[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                        <!--<td>{{Form::checkbox('becaunlps[]', 1,false)}}</td>
                                                        <td><a href="#" class="btn btn-danger removeCategoria"><i class="glyphicon glyphicon-remove"></i></a></td>-->
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
            $('#apellido').autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: '{{ route("integrantes.buscarInvestigador") }}', // Asegúrate de que la ruta es correcta
                        dataType: 'json',
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response($.map(data, function(item) {
                                return {
                                    label: item.apellido + ', ' + item.nombre + ' (' + item.cuil + ')',
                                    value: item.apellido,
                                    id: item.id,
                                    apellido: item.apellido,
                                    nombre: item.nombre,
                                    cuil: item.cuil,
                                    email: item.email,
                                    nacimiento: item.nacimiento,
                                    titulo: item.titulo,
                                    titulopost: item.titulopost,
                                    cargo: item.cargo,
                                    unidad_id: item.unidad_id,
                                    carrerainv: item.carrerainv,
                                    sicadi: item.sicadi,
                                    categoria: item.categoria,
                                    beca: item.beca
                                };
                            }));
                        }
                    });
                },
                minLength: 2,
                select: function(event, ui) {
                    $('#apellido').val(ui.item.apellido);
                    $('#nombre').val(ui.item.nombre);
                    $('#cuil').val(ui.item.cuil);
                    $('#email').val(ui.item.email);
                    let nacimiento = ui.item.nacimiento;
                    if(nacimiento){
                        // Actualizar la fecha de egreso
                        let formattedEgreso = new Date(nacimiento).toISOString().split('T')[0];
                        $('#nacimiento').val(formattedEgreso);
                    }




                    //vacío los campos
                    $('#cuerpoTitulo select[name="titulos[]"]').val('').trigger('change');
                    $('#cuerpoTitulo input[name="egresos[]"]').val('');
                    $('#cuerpoPosgrado select[name="tituloposts[]"]').val('').trigger('change');
                    $('#cuerpoPosgrado input[name="egresoposts[]"]').val('');
                    $('#cuerpoCargos select[name="cargos[]"]').val('').trigger('change');
                    $('#cuerpoCargos select[name="deddocs[]"]').val('').trigger('change');
                    /*$('#cuerpoCargos select[name="facultads[]"]').val('').trigger('change');
                    $('#cuerpoCargos select[name="universidads[]"]').val('').trigger('change');*/
                    $('#cuerpoCargos input[name="ingresos[]"]').val('');
                    $('#unidad_id').val('').trigger('change');
                    $('#facultad_id').val('').trigger('change');
                    $('#universidad_id').val('').trigger('change');
                    $('#cuerpoCarrerainvs select[name="carrerainvs[]"]').val('').trigger('change');
                    $('#cuerpoCarrerainvs input[name="carringresos[]"]').val('');
                    $('#cuerpoCarrerainvs select[name="organismos[]"]').val('').trigger('change');
                    $('#cuerpoCategorias select[name="categorias[]"]').val('').trigger('change');
                    $('#cuerpoSicadis select[name="sicadis[]"]').val('').trigger('change');
                    $('#cuerpoBecas select[name="institucions[]"]').val('').trigger('change');
                    $('#cuerpoBecas select[name="becas[]"]').val('').trigger('change');
                    $('#cuerpoBecas input[name="becadesdes[]"]').val('');
                    $('#cuerpoBecas input[name="becahastas[]"]').val('');
                    // Completa el título y egreso
                    if (ui.item.titulo) {
                        let tituloId = ui.item.titulo.id;
                        let tituloNombre = ui.item.titulo.nombre;
                        let egreso = ui.item.titulo.pivot.egreso;

                        // Actualizar el select con el título del investigador seleccionado
                        $('#cuerpoTitulo select[name="titulos[]"]').val(tituloId).trigger('change');

                        if(egreso){
                            // Actualizar la fecha de egreso
                            let formattedEgreso = new Date(egreso).toISOString().split('T')[0];
                            $('#cuerpoTitulo input[name="egresos[]"]').val(formattedEgreso);
                        }

                    }
                    if (ui.item.titulopost) {
                        let titulopostId = ui.item.titulopost.id;
                        let titulopostNombre = ui.item.titulopost.nombre;
                        let egreso = ui.item.titulopost.pivot.egreso;

                        // Actualizar el select con el título del investigador seleccionado
                        $('#cuerpoPosgrado select[name="tituloposts[]"]').val(titulopostId).trigger('change');

                        if(egreso) {
                            // Actualizar la fecha de egreso
                            let formattedEgreso = new Date(egreso).toISOString().split('T')[0];
                            $('#cuerpoPosgrado input[name="egresoposts[]"]').val(formattedEgreso);
                        }
                    }
                    if (ui.item.cargo) {
                        let cargoId = ui.item.cargo.id;
                        let cargoNombre = ui.item.cargo.nombre;
                        let deddoc = ui.item.cargo.pivot.deddoc;
                        let ingreso = ui.item.cargo.pivot.ingreso;
                        let facultadId = ui.item.cargo.pivot.facultad_id;
                        let universidadId = ui.item.cargo.pivot.universidad_id;

                        $('#cuerpoCargos select[name="cargos[]"]').val(cargoId).trigger('change');
                        $('#cuerpoCargos select[name="deddocs[]"]').val(deddoc).trigger('change');
                        /*$('#cuerpoCargos select[name="facultads[]"]').val(facultadId).trigger('change');
                        $('#cuerpoCargos select[name="universidads[]"]').val(universidadId).trigger('change');*/
                        $('#facultad_id').val(facultadId).trigger('change');
                        $('#universidad_id').val(universidadId).trigger('change');
                        if(ingreso) {
                            // Suponiendo que 'ingreso' es una fecha en formato 'Y-m-d
                            let formattedIngreso = new Date(ingreso).toISOString().split('T')[0];
                            // Asignar la fecha formateada al campo de entrada
                            $('#cuerpoCargos input[name="ingresos[]"]').val(formattedIngreso);
                        }


                    }
                    //console.log(ui.item.unidad_id);
                    $('#unidad_id').val(ui.item.unidad_id).trigger('change');
                    if (ui.item.carrerainv) {
                        let carrerainvId = ui.item.carrerainv.id;
                        let carrerainvNombre = ui.item.carrerainv.nombre;

                        let ingreso = ui.item.carrerainv.pivot.ingreso;
                        let organismoId = ui.item.carrerainv.pivot.organismo_id;


                        $('#cuerpoCarrerainvs select[name="carrerainvs[]"]').val(carrerainvId).trigger('change');

                        $('#cuerpoCarrerainvs select[name="organismos[]"]').val(organismoId).trigger('change');


                        if(ingreso) {
                            // Suponiendo que 'ingreso' es una fecha en formato 'Y-m-d
                            let formattedIngreso = new Date(ingreso).toISOString().split('T')[0];
                            // Asignar la fecha formateada al campo de entrada
                            $('#cuerpoCarrerainvs input[name="carringresos[]"]').val(formattedIngreso);
                        }


                    }
                    if (ui.item.categoria) {
                        let categoriaId = ui.item.categoria.id;
                        let categoriaNombre = ui.item.categoria.nombre;
                        $('#cuerpoCategorias select[name="categorias[]"]').val(categoriaId).trigger('change');
                    }
                    if (ui.item.sicadi) {
                        let sicadiId = ui.item.sicadi.id;
                        let sicadiNombre = ui.item.sicadi.nombre;
                        $('#cuerpoSicadis select[name="sicadis[]"]').val(sicadiId).trigger('change');
                    }
                    if (ui.item.beca) {
                        let institucion = ui.item.beca.institucion;
                        let beca = ui.item.beca.beca;
                        let desde = ui.item.beca.desde;
                        let hasta = ui.item.beca.hasta;
                        $('#cuerpoBecas select[name="institucions[]"]').val(institucion).trigger('change');
                        $('#cuerpoBecas select[name="becas[]"]').val(beca).trigger('change');
                        if(desde) {
                            // Suponiendo que 'ingreso' es una fecha en formato 'Y-m-d
                            let formattedDesde = new Date(desde).toISOString().split('T')[0];
                            // Asignar la fecha formateada al campo de entrada
                            $('#cuerpoBecas input[name="becadesdes[]"]').val(formattedDesde);
                        }
                        if(hasta) {
                            // Suponiendo que 'ingreso' es una fecha en formato 'Y-m-d
                            let formattedHasta = new Date(hasta).toISOString().split('T')[0];
                            // Asignar la fecha formateada al campo de entrada
                            $('#cuerpoBecas input[name="becahastas[]"]').val(formattedHasta);
                        }
                    }
                }
            });

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
