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
                                    <li role="presentation"><a href="#universidad" aria-controls="universidad" role="tab" data-toggle="tab">Universidad</a></li>
                                    <li role="presentation"><a href="#investigacion" aria-controls="investigacion" role="tab" data-toggle="tab">Investigación</a></li>
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
                                                    {{ Form::select('genero',[''=>'Seleccionar...','Mujer'=>'Mujer','Mujer-Trans'=>'Mujer-Trans','Travesti'=>'Travesti','Varón'=>'Varón','Varón-Trans'=>'Varón-Trans','No Binarie'=>'No Binarie','Otro'=>'Otro','Prefiero no responder'=>'Prefiero no responder'], $investigador->genero,['class' => 'form-control','id'=>'genero']) }}

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
                                                    {{Form::label('dpto', 'Departamento')}}
                                                    {{Form::text('dpto', $investigador->dpto, ['class' => 'form-control','placeholder'=>'Departamento'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <div class="form-group">
                                                    {{Form::label('cp', 'Código Postal')}}
                                                    {{Form::text('cp', $investigador->cp, ['class' => 'form-control','placeholder'=>'Código Postal'])}}
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
                                                    {{Form::email('email_institucional', $investigador->email_institucional, ['class' => 'form-control','placeholder'=>'Email institucional'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('email_alternativo', 'Email alternativo')}}
                                                    {{Form::email('email_alternativo', $investigador->email_alternativo, ['class' => 'form-control','placeholder'=>'Email alternativo'])}}
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
                                                        <img id="original" src="{{ url('images/sicadi/'.$investigador->foto) }}" height="200">
                                                    @endif
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
                                            <div class="col-md-5">
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
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('cargo_docente', 'Cargo docente')}}
                                                    {{Form::text('cargo_docente', $investigador->cargo_docente, ['class' => 'form-control','placeholder'=>'Cargo docente'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('cargo_dedicacion', 'Dedicación')}}
                                                    {{Form::text('cargo_dedicacion', $investigador->cargo_dedicacion, ['class' => 'form-control','placeholder'=>'Dedicación'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('cargo_ua', 'U. Académica')}}
                                                    {{Form::text('cargo_ua', $investigador->cargo_ua, ['class' => 'form-control','placeholder'=>'U. Académica'])}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="investigacion">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('ui_sigla', 'SIGLA')}}
                                                    {{Form::text('ui_sigla', $investigador->ui_sigla, ['class' => 'form-control','placeholder'=>'SIGLA'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('ui_nombre', 'Lugar de trabajo')}}
                                                    {{Form::text('ui_nombre', $investigador->ui_nombre, ['class' => 'form-control','placeholder'=>'Lugar de trabajo'])}}
                                                </div>
                                            </div>

                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('carrera_cargo', 'Carrera Investigación')}}
                                                    {{Form::text('carrera_cargo', $investigador->carrera_cargo, ['class' => 'form-control','placeholder'=>'Carrera Investigación'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('carrera_empleador', 'Empleador')}}
                                                    {{Form::text('carrera_empleador', $investigador->carrera_empleador, ['class' => 'form-control','placeholder'=>'Empleador'])}}
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
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('carrera_ui_sigla', 'SIGLA')}}
                                                    {{Form::text('carrera_ui_sigla', $investigador->carrera_ui_sigla, ['class' => 'form-control','placeholder'=>'SIGLA'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('carrera_ui_nombre', 'Lugar de trabajo')}}
                                                    {{Form::text('carrera_ui_nombre', $investigador->carrera_ui_nombre, ['class' => 'form-control','placeholder'=>'Lugar de trabajo'])}}
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="categorizacion">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('presentacion_ua', 'U. Académica')}}
                                                    {{Form::text('presentacion_ua', $investigador->presentacion_ua, ['class' => 'form-control','placeholder'=>'U. Académica'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('mecanismo', 'Mecanismo')}}
                                                    {{Form::text('mecanismo', $investigador->mecanismo, ['class' => 'form-control','placeholder'=>'Mecanismo'])}}
                                                </div>
                                            </div>



                                        </div>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('categoria_spu', 'Categoría SPU')}}
                                                    {{Form::text('categoria_spu', $investigador->categoria_spu, ['class' => 'form-control','placeholder'=>'Categoría SPU'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('categoria_solicitada', 'Categoría Solicitada')}}
                                                    {{Form::text('categoria_solicitada', $investigador->categoria_solicitada, ['class' => 'form-control','placeholder'=>'Categoría Solicitada'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('categoria_asignada', 'Categoría Asignada')}}
                                                    {{Form::text('categoria_asignada', $investigador->categoria_asignada, ['class' => 'form-control','placeholder'=>'Categoría Asignada'])}}
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('area', 'Area')}}
                                                    {{Form::text('area', $investigador->area, ['class' => 'form-control','placeholder'=>'Area'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('subarea', 'Subárea')}}
                                                    {{Form::text('subarea', $investigador->subarea, ['class' => 'form-control','placeholder'=>'Subárea'])}}
                                                </div>
                                            </div>

                                        </div>

                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="becario">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('beca_tipo', 'Tipo')}}
                                                    {{Form::text('beca_tipo', $investigador->beca_tipo, ['class' => 'form-control','placeholder'=>'Tipo'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    {{Form::label('beca_entidad', 'Entidad otorgante')}}
                                                    {{Form::text('beca_entidad', $investigador->beca_entidad, ['class' => 'form-control','placeholder'=>'Entidad otorgante'])}}
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
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    {{Form::label('beca_ui_sigla', 'SIGLA')}}
                                                    {{Form::text('beca_ui_sigla', $investigador->beca_ui_sigla, ['class' => 'form-control','placeholder'=>'SIGLA'])}}
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {{Form::label('beca_ui_nombre', 'Lugar de trabajo')}}
                                                    {{Form::text('beca_ui_nombre', $investigador->beca_ui_nombre, ['class' => 'form-control','placeholder'=>'Lugar de trabajo'])}}
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="proyecto">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    {{Form::label('proyecto_entidad', 'Entidad')}}
                                                    {{Form::text('proyecto_entidad', $investigador->proyecto_entidad, ['class' => 'form-control','placeholder'=>'Tipo'])}}
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
    <!-- page script -->
    <script>
        $(document).ready(function () {
            //$('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
            $('.js-example-basic-single').select2();



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

            // Escuchar el envío del formulario
            $('form').on('submit', function() {
                // Establecer cambios realizados como false
                cambiosRealizados = false;
            });

        });



    </script>

@endsection
