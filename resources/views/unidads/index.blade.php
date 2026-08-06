@extends('layouts.app')

@section('headSection')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.css') }}">
    <!-- AdminLTE Skins -->
    <link rel="stylesheet" href="{{ asset('dist/css/skins/_all-skins.min.css') }}">
    <style>
        /* Árbol / grafo de jerarquía de unidades */
        .tree-unidades, .tree-unidades ul {
            list-style: none;
            margin: 0;
            padding-left: 22px;
        }
        .tree-unidades > li { padding-left: 0; }
        .tree-unidades li {
            position: relative;
            padding: 3px 0;
        }
        /* Líneas conectoras */
        .tree-unidades ul > li::before {
            content: "";
            position: absolute;
            top: 0;
            left: -12px;
            border-left: 1px solid #ccc;
            height: 100%;
        }
        .tree-unidades ul > li:last-child::before { height: 15px; }
        .tree-unidades ul > li::after {
            content: "";
            position: absolute;
            top: 15px;
            left: -12px;
            width: 12px;
            border-top: 1px solid #ccc;
        }
        .tree-unidades .nodo {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .tree-unidades .nodo:hover { background: #f5f5f5; }
        .tree-unidades .toggle { cursor: pointer; color: #3c8dbc; width: 14px; }
        .tree-unidades .hoja { color: #bbb; width: 14px; font-size: 8px; }
        .tree-unidades li.colapsado > ul { display: none; }
    </style>
@endsection

@section('content')
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-sitemap" aria-hidden="true"></i> Unidades
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('unidads.index') }}">Unidades</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            @php
                $hijosPorPadre = $unidads->groupBy('padre_id');
                $idsExistentes = $unidads->pluck('id');
                // Raíces: sin padre, o cuyo padre no existe en el conjunto (huérfanas).
                $raices = $unidads->filter(function ($u) use ($idsExistentes) {
                    return empty($u->padre_id) || ! $idsExistentes->contains($u->padre_id);
                });
            @endphp
            <div class="row">
                <div class="col-xs-12">
                    <div class="box box-primary collapsed-box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-sitemap"></i> Grafo de jerarquía</h3>
                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i></button>
                            </div>
                        </div>
                        <div class="box-body">
                            @if ($raices->count())
                                @include('unidads.partials._arbol', ['nodos' => $raices, 'hijosPorPadre' => $hijosPorPadre])
                            @else
                                <p class="text-muted">No hay unidades cargadas.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            @can('unidad-crear')
                                <a class='pull-right btn btn-success' href="{{ route('unidads.create') }}">Nuevo</a>
                            @endcan
                        </div>
                        @include('includes.messages')

                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Sigla</th>
                                    <th>Código</th>
                                    <th>Facultad</th>
                                    <th>Unidad padre</th>
                                    <th>Activa</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach ($unidads as $unidad)
                                    <tr>
                                        <td>{{ $unidad->nombre }}</td>
                                        <td>{{ $unidad->sigla }}</td>
                                        <td>{{ $unidad->codigo }}</td>
                                        <td>{{ $unidad->facultad_id ? ($facultades[$unidad->facultad_id] ?? '') : '' }}</td>
                                        <td>{{ optional($unidad->padre)->nombre }}</td>
                                        <td>
                                            @if($unidad->activa)
                                                <span class="label label-success">Sí</span>
                                            @else
                                                <span class="label label-default">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @can('unidad-editar')
                                                <a href="{{ route('unidads.edit', $unidad->id) }}" alt="Editar" title="Editar"><span class="glyphicon glyphicon-edit"></span></a>
                                            @endcan
                                            @can('unidad-eliminar')
                                                <form id="delete-form-{{ $unidad->id }}" method="post" action="{{ route('unidads.destroy', $unidad->id) }}" style="display: none">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                </form>

                                                <a href="" onclick="
                                                    if(confirm('Está seguro?'))
                                                    {
                                                    event.preventDefault();
                                                    document.getElementById('delete-form-{{ $unidad->id }}').submit();
                                                    }
                                                    else{
                                                    event.preventDefault();
                                                    }" alt="Eliminar" title="Eliminar"><span class="glyphicon glyphicon-trash"></span></a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Sigla</th>
                                    <th>Código</th>
                                    <th>Facultad</th>
                                    <th>Unidad padre</th>
                                    <th>Activa</th>
                                    <th>Acciones</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
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
    <!-- DataTables -->
    <script src="{{ asset('bower_components/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ asset('bower_components/jquery-slimscroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- page script -->
    <script>
        $(document).ready(function() {
            $('#example1').DataTable({
                "autoWidth": false,
                responsive: true,
                scrollX: true,
                "language": {
                    "url": "{{ asset('bower_components/datatables.net/lang/es-AR.json') }}"
                },
                stateSave: true,
            });

            // Plegar / desplegar ramas del árbol de jerarquía
            $('.tree-unidades').on('click', '.toggle', function (e) {
                e.preventDefault();
                var $li = $(this).closest('li');
                $li.toggleClass('colapsado');
                $(this).toggleClass('fa-caret-down fa-caret-right');
            });
        });
    </script>
@endsection
