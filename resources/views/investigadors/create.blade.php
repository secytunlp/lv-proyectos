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
                            <h3 class="box-title">Crear</h3>
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
                                    <!-- Agrega más pestañas según sea necesario -->
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="datos_personales">

                                        <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="apellido">Apellido</label>
                                                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido" value="{{ old('apellido') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="nombre">Nombre</label>
                                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('documento', 'Documento')}}
                                                {{Form::number('documento', '', ['class' => 'form-control','placeholder'=>'Documento'])}}
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                {{Form::label('cuil', 'CUIL')}}
                                                {{Form::text('cuil', '', ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X'])}}
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
                                                {{Form::date('nacimiento', '', ['class' => 'form-control'])}}
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
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('calle', 'Calle')}}
                                                    {{Form::text('calle', '', ['class' => 'form-control','placeholder'=>'Calle'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('nro', 'Número')}}
                                                    {{Form::text('nro', '', ['class' => 'form-control','placeholder'=>'Número'])}}

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('piso', 'Piso')}}
                                                    {{Form::text('piso', '', ['class' => 'form-control','placeholder'=>'Piso'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
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


                                            <table class="table" style="width: 50%">
                                                <thead>

                                                <th>Título</th>
                                                <th>Egreso</th>
                                                <th><a href="#" class="addRow"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                </thead>

                                                <tbody id="cuerpoTitulo">
                                                <tr>

                                                    <td>{{ Form::select('titulos[]',$titulos, '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px']) }}</td>
                                                    <td>{{Form::date('egresos[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>

                                                    <td><a href="#" class="btn btn-danger remove"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                </tr>

                                                </tbody>




                                            </table>
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


                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Título</th>
                                                    <th>Egreso</th>
                                                    <th><a href="#" class="addRowPost"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                    </thead>

                                                    <tbody id="cuerpoPosgrado">
                                                    <tr>

                                                        <td>{{ Form::select('tituloposts[]',$tituloposts, '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px']) }}</td>
                                                        <td>{{Form::date('egresoposts[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>

                                                        <td><a href="#" class="btn btn-danger removePost"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                    </tr>

                                                    </tbody>




                                                </table>
                                            </div>
                                        </fieldset>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Cargos Docentes</legend>

                                            <div class="form-group col-md-12">


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
                                                    <tr>

                                                        <td>{{ Form::select('cargos[]',$cargos, '',['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                        <td>{{ Form::select('deddocs[]',['','Exclusiva','Semi Exclusiva','Simple'], '',['class' => 'form-control', 'style' => 'width: 120px']) }}</td>
                                                        <td>{{Form::date('ingresos[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>
                                                        <td>{{ Form::select('facultads[]',$facultades, '',['class' => 'form-control', 'style' => 'width: 300px']) }}</td>
                                                        <td>{{ Form::select('universidads[]',$universidades, '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px']) }}</td>
                                                        <td>{{Form::checkbox('activos[]', 1,true)}}</td>
                                                        <td><a href="#" class="btn btn-danger removeCargo"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                    </tr>

                                                    </tbody>




                                                </table>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div role="tabpanel" class="tab-pane active" id="investigacion">
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    {{Form::label('unidad', 'Lugar de Trabajo')}}
                                                    {{Form::select('unidad_id',  $unidads,'', ['class' => 'form-control js-example-basic-single','id'=>'unidad_id'])}}

                                                </div>
                                            </div>


                                        </div>
                                        <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                            <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Carrera de Investigación</legend>

                                            <div class="form-group col-md-12">


                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Cargo</th>
                                                    <th>Institución</th>
                                                    <th>Ingreso</th>

                                                    <th>Actual</th>
                                                    <th><a href="#" class="addRowCarrerainv"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                    </thead>

                                                    <tbody id="cuerpoCarrerainvs">
                                                    <tr>

                                                        <td>{{ Form::select('carrerainvs[]',$carrerainvs, '',['class' => 'form-control', 'style' => 'width: 200px']) }}</td>
                                                        <td>{{ Form::select('organismos[]',$organismos, '',['class' => 'form-control', 'style' => 'width: 150px']) }}</td>
                                                        <td>{{Form::date('carringresos[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}</td>


                                                        <td>{{ Form::radio('actual', 1, true,['id' => 'actual_1']) }}</td> <!-- Usamos un nombre único con el índice 1 -->
                                                        <td><a href="#" class="btn btn-danger removeCarrerainv"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                    </tr>

                                                    </tbody>




                                                </table>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href='{{ route('investigadors.index') }}' class="btn btn-warning">Volver</a>
                                    </div>
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
    <!-- page script -->
    <script>
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

            // Limpiar el estado del radio button por defecto
            $('input[name="actual"]').prop('checked', false);

            // Seleccionar el radio button por defecto
            $('#actual_1').prop('checked', true);

            // Bandera para rastrear si se han realizado cambios en el formulario
            var cambiosRealizados = false;

            // Escuchar cambios en los campos del formulario
            $('input, select, textarea').on('change', function() {
                cambiosRealizados = true;
            });

            // Mostrar mensaje de advertencia cuando el usuario intente abandonar la página
            window.addEventListener('beforeunload', function (event) {
                // Verificar si se han realizado cambios en el formulario
                if (cambiosRealizados) {
                    // Personaliza el mensaje de advertencia según tus necesidades
                    var mensaje = "¡Atención! Puede perder algunos cambios. ¿Estás seguro de abandonar la página?";

                    // Establecer el mensaje de advertencia en el evento
                    event.returnValue = mensaje;

                    // Devolver el mensaje de advertencia (solo necesario en algunos navegadores antiguos)
                    return mensaje;
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
                '<td>'+'{{ Form::select('titulos[]',$titulos ?? [''=>''], '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px']) }}'+'</td>'+
                '<td>'+'{{Form::date('egresos[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}'+'</td>'+

                '<td><a href="#" class="btn btn-danger remove"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoTitulo').append(tr);
            $('.js-example-basic-single').select2();
        };

        $('body').on('click', '.remove', function(e){

            e.preventDefault();
            $(this).parent().parent().remove();
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
                '<td>'+'{{ Form::select('tituloposts[]',$tituloposts ?? [''=>''], '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 400px']) }}'+'</td>'+
                '<td>'+'{{Form::date('egresoposts[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}'+'</td>'+

                '<td><a href="#" class="btn btn-danger removePost"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoPosgrado').append(tr);
            $('.js-example-basic-single').select2();
        };

        $('body').on('click', '.removePost', function(e){

            e.preventDefault();
            $(this).parent().parent().remove();


        });

        $('.addRowCargo').on('click',function(e){
            e.preventDefault();
            addRowCargo();
        });
        function addRowCargo()
        {
            var tr='<tr>'+
                '<td>'+'{{ Form::select('cargos[]',$cargos ?? [''=>''], '',['class' => 'form-control', 'style' => 'width: 200px']) }}'+'</td>'+
                '<td>'+'{{ Form::select('deddocs[]',['','Exclusiva','Semi Exclusiva','Simple'] ?? [''=>''], '',['class' => 'form-control', 'style' => 'width: 120px']) }}'+'</td>'+
                '<td>'+'{{Form::date('ingresos[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}'+'</td>'+
                '<td>'+'{{ Form::select('facultads[]',$facultades ?? [''=>''], '',['class' => 'form-control', 'style' => 'width: 300px']) }}'+'</td>'+
                '<td>'+'{{ Form::select('universidads[]',$universidades ?? [''=>''], '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px']) }}'+'</td>'+
                '<td>'+'{{ Form::checkbox('activos[]',1,true) }}'+'</td>'+
                '<td><a href="#" class="btn btn-danger removeCargo"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoCargos').append(tr);
            $('.js-example-basic-single').select2();

        };

        $('body').on('click', '.removeCargo', function(e){

            e.preventDefault();
            $(this).parent().parent().remove();


        });

        $('.addRowCarrerainv').on('click',function(e){
            e.preventDefault();
            addRowCarrerainv();
        });
        function addRowCarrerainv()
        {
            var tr='<tr>'+
                '<td>'+'{{ Form::select('carrerainvs[]',$carrerainvs ?? [''=>''], '',['class' => 'form-control', 'style' => 'width: 200px']) }}'+'</td>'+
                '<td>'+'{{ Form::select('organismos[]',$organismos ?? [''=>''], '',['class' => 'form-control', 'style' => 'width: 150px']) }}'+'</td>'+
                '<td>'+'{{Form::date('carringresos[]', '', ['class' => 'form-control', 'style' => 'width:150px;'])}}'+'</td>'+


                '<td><input type="radio" name="actual" id="actual_' + ($("input[id^=\'actual_\']").length + 1) + '" value="' + ($("input[id^=\'actual_\']").length + 1) + '"></td>' +


                '<td><a href="#" class="btn btn-danger removeCarrerainv"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                '</tr>';
            $('#cuerpoCarrerainvs').append(tr);



        };

        $('body').on('click', '.removeCarrerainv', function(e){

            e.preventDefault();
            $(this).parent().parent().remove();


        });

    </script>

@endsection