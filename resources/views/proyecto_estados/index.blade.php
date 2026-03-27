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
                <i class="fa fa-tasks" aria-hidden="true"></i> Estados

            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('proyectos.index') }}">Proyectos</a></li>
                <li><a href="{{ route('proyecto_estados.index') }}">Estados</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Estados de @if($proyecto) {{ $proyecto->codigo }} {{ $proyecto->titulo }}@endif</h3>
                            @if($proyecto)<a class='pull-right btn btn-success' href="{{ route('proyecto_estados.create', ['proyecto_id' => $proyecto->id]) }}">Cambiar</a>@endif
                        </div>
                        @include('includes.messages')


                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Tipo</th>
                                    <th>Código</th>
                                    <th>Título</th>
                                    <th>Director</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>

                                    <th>U. Académica</th>
                                    <th>Estado</th>

                                    <th>Desde</th>
                                    <th>Hasta</th>
                                    <th>Comentarios</th>
                                    <th>Usuario</th>

                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Tipo</th>
                                    <th>Código</th>
                                    <th>Título</th>
                                    <th>Director</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>

                                    <th>U. Académica</th>
                                    <th>Estado</th>

                                    <th>Desde</th>
                                    <th>Hasta</th>
                                    <th>Comentarios</th>
                                    <th>Usuario</th>

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
            $('#example1').DataTable({
                "processing": true, // Activar la indicación de procesamiento
                "serverSide": true, // Habilitar el procesamiento del lado del servidor
                "autoWidth": false, // Desactiva el ajuste automático del anchos
                responsive: true,
                scrollX: true,
                paging : true,
                "order": [[ 9, "desc" ]], // Ordenar por el tercer campo (apellido) de forma ascendente
                "ajax": {
                    "url": "{{ route('proyecto_estados.dataTable') }}",
                    "type": "POST",
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}'; // Agrega el token CSRF si estás usando Laravel
                        d.proyecto_id = '{{ $proyecto ? $proyecto->id : '' }}'; // Enviar el ID del proyecto como filtro
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

                    {data: 'proyecto_tipo', name: 'proyecto_tipo'},
                    {data: 'codigo', name: 'codigo'},
                    {data: 'titulo', name: 'titulo'},
                    {data: 'director', name: 'director'},

                    {
                        data: 'inicio',
                        name: 'inicio',
                        render: function(data) {
                            // Verificar si la fecha de baja es null
                            if (data === null || data === '0000-00-00') {
                                return ''; // Devolver una cadena vacía si es null o un valor predeterminado no válido
                            }
                            // Formatear la fecha de inicio en Y-m-d (sin hora)
                            return moment(data).format('DD/MM/YYYY');
                        }
                    },
                    {
                        data: 'fin',
                        name: 'fin',
                        render: function(data) {
                            // Verificar si la fecha de baja es null
                            if (data === null || data === '0000-00-00') {
                                return ''; // Devolver una cadena vacía si es null o un valor predeterminado no válido
                            }
                            // Formatear la fecha de fin en Y-m-d (sin hora)
                            return moment(data).format('DD/MM/YYYY');
                        }
                    },


                    {
                        data: 'facultad_nombre', // Acceder al nombre de la facultad a través de la relación
                        name: 'facultad_nombre',
                        orderable: true,
                        searchable: true
                    },
                    {data: 'estado', name: 'estado'},


                    {
                        data: 'desde',
                        name: 'desde',
                        render: function(data) {
                            // Verificar si la fecha es null
                            if (data === null || data === '0000-00-00') {
                                return '';
                            }
                            // Formatear la fecha de 'desde' en DD/MM/YYYY HH:mm
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    },
                    {
                        data: 'hasta',
                        name: 'hasta',
                        render: function(data) {
                            // Verificar si la fecha es null
                            if (data === null || data === '0000-00-00') {
                                return '';
                            }
                            // Formatear la fecha de 'hasta' en DD/MM/YYYY HH:mm
                            return moment(data).format('DD/MM/YYYY HH:mm');
                        }
                    },

                    {data: 'comentarios', name: 'comentarios'},
                    {
                        data: 'name', // Acceder al nombre de la usuario a través de la relación
                        name: 'name',
                        orderable: true,
                        searchable: true
                    },

                    {
                        "data": "id",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, row) {
                            // Construir HTML para las acciones
                            var actionsHtml = '';



                        return actionsHtml;

                    },
            }
                ],
                "language": {
                    "url": "{{ asset('bower_components/datatables.net/lang/es-AR.json') }}"
                },
                stateSave: true
            });
        });

    </script>
@endsection
