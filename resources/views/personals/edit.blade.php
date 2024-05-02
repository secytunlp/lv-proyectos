@extends('layouts.app')
@section('headSection')

    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">

@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-user-md" aria-hidden="true"></i> Personal
                <small>Editar</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('personals.index') }}">Personal</a></li>
                <!--<li class="active">Edit Form</li>-->
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
                        <form role="form" action="{{ route('personals.update',$personal->id) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="box-body">
                                @include('includes.messages')
                                <div class="row">
                                    <div class="col-lg-offset-3 col-lg-6 col-md-4">

                                        <div class="form-group">
                                            <label for="nombre">Nombre</label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" value="@if (old('nombre')){{ old('nombre') }}@else{{ $personal->persona->nombre }}@endif">
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-4">
                                        <div class="form-group">
                                            <label for="apellido">Apellido</label>
                                            <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Apellido" value="@if (old('apellido')){{ old('apellido') }}@else{{ $personal->persona->apellido }}@endif">
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-2">
                                        <div class="form-group">
                                            {{Form::label('tipoDocumento', 'Tipo')}}
                                            {{ Form::select('tipoDocumento',['DNI'=>'DNI','PAS'=>'Pasaporte','CI'=>'Cedula'], $personal->persona->tipoDocumento,['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-2">
                                        <div class="form-group">
                                            {{Form::label('documento', 'Documento')}}
                                            {{Form::text('documento', $personal->persona->documento, ['class' => 'form-control','placeholder'=>'Documento'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-lg-6 col-md-2">
                                        <div class="form-group">
                                            {{Form::label('cuil', 'Cuil')}}
                                            {{Form::text('cuil', $personal->persona->cuil, ['class' => 'form-control','placeholder'=>'Cuil'])}}
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-2">
                                        <div class="form-group">
                                            <label for="genero">Género</label>
                                            {{ Form::select('genero',[''=>'','M'=>'M','F'=>'F','X'=>'X'], $personal->persona->genero,['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-4">
                                        <div class="form-group">
                                            {{Form::label('email', 'E-mail')}}
                                            {{Form::email('email', $personal->persona->email, ['class' => 'form-control','placeholder'=>'E-mail'])}}
                                        </div>
                                    </div>

                                    <div class="col-lg-offset-3 col-lg-6 col-md-4">
                                        <div class="form-group">
                                            {{Form::label('domicilio', 'Domicilio')}}
                                            {{Form::text('domicilio', $personal->persona->domicilio, ['class' => 'form-control','placeholder'=>'Domicilio'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-lg-6 col-md-2">
                                        <div class="form-group">
                                            {{Form::label('telefono', 'Teléfono')}}
                                            {{Form::text('telefono', $personal->persona->telefono, ['class' => 'form-control','placeholder'=>'Teléfono'])}}
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-3">
                                        <div class="form-group">
                                            {{Form::label('nacimiento', 'Nacimiento')}}
                                            {{Form::date('nacimiento', ($personal->persona->nacimiento)?date('Y-m-d', strtotime($personal->persona->nacimiento)):'', ['class' => 'form-control'])}}
                                        </div>
                                    </div>

                                    <div class="col-lg-offset-3 col-lg-6 col-md-3">
                                        <div class="form-group">
                                            {{Form::label('ingreso', 'Ingreso')}}
                                            {{Form::date('ingreso', ($personal->ingreso)?date('Y-m-d', strtotime($personal->ingreso)):'', ['class' => 'form-control'])}}
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-3">
                                        <div class="form-group">
                                            {{Form::label('baja', 'Baja')}}
                                            {{Form::date('baja', ($personal->baja)?date('Y-m-d', strtotime($personal->baja)):'', ['class' => 'form-control'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-lg-6 col-md-4">
                                        {{Form::label('ocupacion', 'Ocupación')}}
                                        {{Form::select('ocupacion_id', $ocupacions,($personal->ocupacion)?$personal->ocupacion->id:'', ['class' => 'form-control'])}}

                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-4">

                                        <div class="form-group">
                                            {{Form::label('matricula', 'Matricula')}}
                                            {{Form::text('matricula', $personal->matricula, ['class' => 'form-control','placeholder'=>'Matricula'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-lg-6 col-md-4">

                                        <div class="form-group">
                                            <label for="foto">Foto</label>
                                            @if($personal->persona->foto)
                                                <img id="original" src="{{ url('images/'.$personal->persona->foto) }}" height="200">
                                            @endif
                                            <input type="file" name="foto" class="form-control" placeholder="">

                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-8">

                                        <div class="form-group">
                                            {{Form::label('observaciones', 'Observaciones')}}
                                            {{Form::textarea('observaciones',  $personal->persona->observaciones, ['class' => 'form-control'])}}

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                    <a href='{{ route('personals.index') }}' class="btn btn-warning">Volver</a>
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
    <!-- FastClick -->
    <script src="{{ asset('bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- page script -->

@endsection
