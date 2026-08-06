@extends('layouts.app')

@section('headSection')
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('bower_components/select2/dist/css/select2.min.css') }}">
    <!-- AdminLTE Skins -->
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-sitemap" aria-hidden="true"></i> Unidad
                <small>Crear</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('unidads.index') }}">Unidades</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border"></div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="{{ route('unidads.store') }}" method="post">
                            {{ csrf_field() }}
                            <div class="box-body">
                                @include('includes.messages')
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nombre">Nombre <span class="text-red">*</span></label>
                                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" value="{{ old('nombre') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sigla">Sigla</label>
                                            <input type="text" class="form-control" id="sigla" name="sigla" placeholder="Sigla" maxlength="15" value="{{ old('sigla') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="codigo">Código</label>
                                            <input type="text" class="form-control" id="codigo" name="codigo" placeholder="Código" maxlength="15" value="{{ old('codigo') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="facultad_id">Facultad</label>
                                            <select class="form-control select2" style="width:100%" id="facultad_id" name="facultad_id" data-placeholder="-- Seleccione --">
                                                <option value="">-- Seleccione --</option>
                                                @foreach ($facultades as $id => $nombre)
                                                    <option value="{{ $id }}" {{ old('facultad_id') == $id ? 'selected' : '' }}>{{ $nombre }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="padre_id">Unidad padre</label>
                                            <select class="form-control select2" style="width:100%" id="padre_id" name="padre_id" data-placeholder="-- Ninguna --">
                                                <option value="">-- Ninguna --</option>
                                                @foreach ($unidadesPadre as $padre)
                                                    <option value="{{ $padre->id }}" {{ old('padre_id') == $padre->id ? 'selected' : '' }}>{{ $padre->path_to_parent }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="direccion">Dirección</label>
                                            <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Dirección" value="{{ old('direccion') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="{{ old('email') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="telefono">Teléfono</label>
                                            <input type="text" class="form-control" id="telefono" name="telefono" placeholder="Teléfono" maxlength="15" value="{{ old('telefono') }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="tipo">Tipo (código)</label>
                                            <input type="number" class="form-control" id="tipo" name="tipo" placeholder="Tipo" value="{{ old('tipo') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="activa">Activa</label>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" id="activa" name="activa" value="1" {{ old('activa') ? 'checked' : '' }}> Unidad activa
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                    <a href='{{ route('unidads.index') }}' class="btn btn-warning">Volver</a>
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
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- Select2 -->
    <script src="{{ asset('bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(function () {
            $('.select2').select2({
                allowClear: true,
                placeholder: function () {
                    return $(this).data('placeholder');
                }
            });
        });
    </script>
@endsection
