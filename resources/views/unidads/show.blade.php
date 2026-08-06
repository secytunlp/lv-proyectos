@extends('layouts.app')

@section('headSection')
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
                <small>Detalle</small>
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
                        <div class="box-body">
                            <table class="table table-bordered">
                                <tr><th style="width:220px">Nombre</th><td>{{ $unidad->nombre }}</td></tr>
                                <tr><th>Sigla</th><td>{{ $unidad->sigla }}</td></tr>
                                <tr><th>Código</th><td>{{ $unidad->codigo }}</td></tr>
                                <tr><th>Facultad</th><td>{{ $unidad->facultad_id ? ($facultades[$unidad->facultad_id] ?? '') : '' }}</td></tr>
                                <tr><th>Unidad padre</th><td>{{ optional($unidad->padre)->nombre }}</td></tr>
                                <tr><th>Dirección</th><td>{{ $unidad->direccion }}</td></tr>
                                <tr><th>Email</th><td>{{ $unidad->email }}</td></tr>
                                <tr><th>Teléfono</th><td>{{ $unidad->telefono }}</td></tr>
                                <tr><th>Tipo (código)</th><td>{{ $unidad->tipo }}</td></tr>
                                <tr><th>Activa</th><td>{{ $unidad->activa ? 'Sí' : 'No' }}</td></tr>
                                <tr>
                                    <th>Unidades hijas</th>
                                    <td>
                                        @forelse ($unidad->hijas as $hija)
                                            <span class="label label-info">{{ $hija->nombre }}</span>
                                        @empty
                                            <em>Sin unidades hijas</em>
                                        @endforelse
                                    </td>
                                </tr>
                            </table>

                            <div class="form-group">
                                @can('unidad-editar')
                                    <a href='{{ route('unidads.edit', $unidad->id) }}' class="btn btn-primary">Editar</a>
                                @endcan
                                <a href='{{ route('unidads.index') }}' class="btn btn-warning">Volver</a>
                            </div>
                        </div>
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
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
@endsection
