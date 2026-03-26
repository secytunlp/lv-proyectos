@extends('layouts.app')

@section('headSection')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.css') }}">
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
                <i class="fa fa-user-friends" aria-hidden="true"></i> Miembros

            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('miembros.index') }}">Miembros</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Miembros @if($unidad) - {{ $unidad->denominacion }} - {{ $unidad->sigla }}@endif</h3>
                            @if($unidad)<a class='pull-right btn btn-success' href="{{ route('miembros.create', ['unidad_id' => $unidad->id]) }}">Nuevo</a>@endif
                        </div>
                        @include('includes.messages')


                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Investigador</th>
                                    <th>Unidad</th>
                                    <th>Sigla</th>
                                    <th>Tipo</th>

                                    <th>Investigador</th>
                                    <th>CUIL</th>
                                    <!--<th>Categoria</th>

                                    <th>SICADI</th>
                                    <th>Cargo</th>
                                    <th>Dedicación</th>
                                    <th>Beca</th>
                                    <th>Institución</th>
                                    <th>Carrera Investigador</th>
                                    <th>Organismo</th>
                                    <th>Alta</th>
                                    <th>Baja</th>-->
                                    <th>U. Académica</th>

                                    <th>Horas</th>
                                    <th>Estado</th>
                                    <th>Activo</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Investigador</th>
                                    <th>Unidad</th>
                                    <th>Sigla</th>
                                    <th>Tipo</th>

                                    <th>Investigador</th>
                                    <th>CUIL</th>
                                    <!--<th>Categoria</th>

                                    <th>SICADI</th>
                                    <th>Cargo</th>
                                    <th>Dedicación</th>
                                    <th>Beca</th>
                                    <th>Institución</th>
                                    <th>Carrera Investigador</th>
                                    <th>Organismo</th>
                                    <th>Alta</th>
                                    <th>Baja</th>-->
                                    <th>U. Académica</th>

                                    <th>Horas</th>
                                    <th>Estado</th>
                                    <th>Activo</th>
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
    <!-- FastClick -->
    <script src="{{ asset('bower_components/fastclick/lib/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- page script -->
    <script>
        $(document).ready(function() {
            var table = $('#example1').DataTable({
                "processing": true, // Activar la indicación de procesamiento
                "serverSide": true, // Habilitar el procesamiento del lado del servidor
                "autoWidth": false, // Desactiva el ajuste automático del anchos
                responsive: true,
                scrollX: true,
                paging : true,
                "order": [[ 2, "asc" ]], // Ordenar por el tercer campo (apellido) de forma ascendente
                "ajax": {
                    "url": "{{ route('miembros.dataTable') }}",
                    "type": "POST",
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}'; // Agrega el token CSRF si estás usando Laravel
                        d.unidad_id = '{{ $unidad ? $unidad->id : '' }}'; // Enviar el ID del unidad como filtro
                        // Agrega otros parámetros si es necesario
                        // d.otroParametro = valor;
                    },
                    "error": function(xhr, error, thrown) {
                        if (xhr.status === 401) {
                            // Usuario no autenticado, redirigir al login
                            window.location.href = "{{ route('login') }}";
                        } else {
                            console.error("Error al cargar los datos:", error);
                        }
                    }
                },
                columns: [

                    {data: 'persona_nombre', name: 'persona_nombre', visible:false},
                    {data: 'denominacion', name: 'denominacion'},
                    {data: 'sigla', name: 'sigla'},
                    {data: 'tipo', name: 'tipo'},
                    {data: 'persona_apellido', name: 'persona_apellido'},
                    {data: 'cuil', name: 'cuil'},
                    /*{
                        data: 'categoria_nombre', // Acceder al nombre de la categoria a través de la relación
                        name: 'categoria_nombre',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'sicadi_nombre', // Acceder al nombre de la sicadi a través de la relación
                        name: 'sicadi_nombre',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'cargo_nombre', // Acceder al nombre de la cargo a través de la relación
                        name: 'cargo_nombre',
                        orderable: true,
                        searchable: true
                    },
                    {data: 'deddoc', name: 'deddoc'},
                    {data: 'beca', name: 'beca'},
                    {data: 'institucion', name: 'institucion', visible:false},
                    {
                        data: 'carrerainv_nombre', // Acceder al nombre de la carrerainv a través de la relación
                        name: 'carrerainv_nombre',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'organismo_nombre', // Acceder al nombre de la organismo a través de la relación
                        name: 'organismo_nombre',
                        orderable: true,
                        searchable: true,
                        visible: false
                    },
                    {
                        data: 'alta',
                        name: 'alta',
                        render: function(data) {
                            // Formatear la fecha de alta en Y-m-d (sin hora)
                            return moment(data).format('DD/MM/YYYY');
                        }
                    },
                    {
                        data: 'baja',
                        name: 'baja',
                        render: function(data) {
                            // Verificar si la fecha de baja es null
                            if (data === null || data === '0000-00-00') {
                                return ''; // Devolver una cadena vacía si es null o un valor predeterminado no válido
                            }

                            // Formatear la fecha de baja en DD/MM/YYYY
                            return moment(data).format('DD/MM/YYYY');
                        }
                    },*/

                    {
                        data: 'facultad_nombre', // Acceder al nombre de la facultad a través de la relación
                        name: 'facultad_nombre',
                        orderable: true,
                        searchable: true
                    },

                    {data: 'horas', name: 'horas'},


                    {data: 'estado', name: 'estado'},
                    {data: 'activo', name: 'activo'},
                    {
                        "data": "id",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, row) {
                            // Construir HTML para las acciones

                            var actionsHtml = '';

// Agregar enlace de edición si el usuario tiene permiso
                            @can('miembro-listar')
                                actionsHtml += '<a href="{{ route("miembros.show", ":id") }}" alt="Ver miembro" title="Ver miembro" style="margin-right: 5px;"><span class="glyphicon glyphicon-eye-open"></span></a>'.replace(':id', row.id);
                            @endcan
                            @can('miembro_estado-listar')
                                actionsHtml += '<a href="{{ route("miembro_estados.index") }}?miembro_id=' + row.id + '" alt="Estados" title="Estados" style="margin-right: 5px;"><i class="fa fa-tasks"></i></a>';
                            @endcan




                            //console.log(row.estado);
                            if(row.estado == 'Alta Creada') {
                                @can('miembro-editar')
                                    actionsHtml += '<a href="{{ route("miembros.edit", ":id") }}" alt="Modificar alta" title="Modificar alta" style="margin-right: 5px;"><span class="glyphicon glyphicon-edit"></span></a>'.replace(':id', row.id);
                                @endcan
                                // Agregar formulario de eliminación si el usuario tiene permiso
                                @can('miembro-eliminar')
                                    actionsHtml += '<form id="delete-form-' + row.id + '" method="post" action="{{ route('miembros.destroy', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';
                                actionsHtml += '{{ method_field('DELETE') }}';
                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'delete-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Anular alta" title="Anular alta" style="margin-right: 5px;"><span class="glyphicon glyphicon-trash"></span></a>';
                                @endcan
                            }






                            return actionsHtml;

                    },
            }
                ],
                "language": {
                    "url": "{{ asset('bower_components/datatables.net/lang/es-AR.json') }}"
                },
                stateSave: true,

                "initComplete": function() {

                }
            });
        });

    </script>
@endsection
