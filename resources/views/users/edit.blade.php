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
                <i class="fa fa-user" aria-hidden="true"></i> Usuario
                <small>Editar</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('users.index') }}">Usuarios</a></li>
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
                        <form role="form" action="{{ route('users.update',$user->id) }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            {{ method_field('PUT') }}
                            <div class="box-body">

                                    @include('includes.messages')
                                <div class="row">
                                    <div class="col-lg-offset-3 col-lg-6 col-md-3">
                                        <div class="form-group">
                                            <label for="name">Nombre</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Nombre" value="@if (old('name')){{ old('name') }}@else{{ $user->name }}@endif">
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-4">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" id="email" name="email" placeholder="email" value="@if (old('email')){{ old('email') }}@else{{ $user->email }}@endif">
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-4">
                                        <div class="form-group">
                                            <label for="cuil">CUIL</label>
                                            <input type="text" class="form-control" id="cuil" name="cuil" placeholder="XX-XXXXXXXX-X" value="@if (old('cuil')){{ old('cuil') }}@else{{ $user->cuil }}@endif">
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-5">
                                        <div class="form-group">
                                            <label for="facultad_id">U. Académica</label>
                                            <select name="facultad_id" class="form-control js-example-basic-single">
                                                <option value="">Seleccionar UA</option>
                                                @foreach($facultades as $facultadId => $facultad)
                                                    <option value="{{ $facultadId }}" {{ old('facultad_id', $userFacultad) == $facultadId ? 'selected' : '' }}>{{ $facultad }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-2">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="clave" value="{{ old('password') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-2">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirmar clave </label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmar clave">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-lg-6 col-md-5">
                                        <div class="form-group">
                                            <label for="foto">Foto</label>
                                            @if($user->image)
                                                <img id="original" src="{{ asset($user->image) }}" height="200">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="delete_image" value="1">
                                                        Eliminar foto actual
                                                    </label>
                                                </div>
                                            @endif


                                            <input type="file" name="image" class="form-control" placeholder="">

                                        </div>
                                    </div>

                                    <div class="col-lg-offset-3 col-lg-6 col-md-5">
                                        <div class="form-group">
                                            <label for="roles">Roles</label>
                                            {!! Form::select('roles[]', $roles,$userRole, array('class' => 'form-control','multiple')) !!}
                                        </div>
                                    </div>
                                </div>


                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href='{{ route('users.index') }}' class="btn btn-warning">Volver</a>
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
    <!-- Inputmask -->
    <script src="{{ asset('bower_components/inputmask/dist/min/jquery.inputmask.bundle.min.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- page script -->
    <script>
        $(document).ready(function () {
            $('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
            $('.js-example-basic-single').select2();
        });
    </script>
@endsection
