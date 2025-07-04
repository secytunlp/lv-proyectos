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
                <i class="fa fa-th-list" aria-hidden="true"></i> Evaluaciones Jóvenes Investigadores

            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('joven_evaluacions.index') }}">Evaluaciones</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Evaluaciones @if($joven) - {{ $joven->investigador->persona->apellido }} {{ $joven->investigador->persona->nombre }} - Período - {{ $joven->periodo->nombre }}@endif</h3>
                            @if($joven)

                                @can('integrante_estado-crear')
                                    <a style="margin-left: 5px;" class='pull-right btn btn-danger' href="{{ route('joven_evaluacions.actualizar', ['joven_id' => $joven->id]) }}">Actualizar puntaje</a>
                                @endcan
                                <a style="margin-left: 5px;" class='pull-right btn btn-info' href="{{ route('joven_evaluacions.enviar', ['joven_id' => $joven->id]) }}" onclick="return confirmarEnvio();">Enviar a Evaluadores</a>

                                <a  class='pull-right btn btn-success ml' href="{{ route('joven_evaluacions.create', ['joven_id' => $joven->id]) }}">Asignar Evaluadores</a>

                            @endif
                        </div>
                        @include('includes.messages')
                        @if(!$joven)
                        <!-- Filtro de Período -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="filtroPeriodo">Período:</label>
                                        <select id="filtroPeriodo" class="form-control">
                                            <option value="">Todos</option>
                                            @foreach($periodos as $periodo)
                                                <option value="{{ $periodo->id }}"
                                                    {{ $periodo->nombre == $currentPeriod ? 'selected' : '' }}>
                                                    {{ $periodo->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="filtroPredefinido">Predefinidos:</label>
                                        <select id="filtroPredefinido" class="form-control">
                                            <option value="">Seleccionar...</option>
                                            <option value="2">Faltan evaluadores</option>
                                            <option value="3">Sin actualizar puntaje</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <!-- /.form-group -->
                        </div>

                        @endif
                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Investigador</th>
                                    <th>Período</th>


                                    <th>Investigador</th>
                                    <th>Fecha</th>

                                    <th>Evaluador</th>
                                    <th>Interno</th>


                                    <th>Estado</th>
                                    <th>Puntaje</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Investigador</th>
                                    <th>Período</th>


                                    <th>Investigador</th>
                                    <th>Fecha</th>

                                    <th>Evaluador</th>
                                    <th>Interno</th>


                                    <th>Estado</th>
                                    <th>Puntaje</th>
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
                    "url": "{{ route('joven_evaluacions.dataTable') }}",
                    "type": "POST",
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}'; // Agrega el token CSRF si estás usando Laravel
                        d.joven_id = '{{ $joven ? $joven->id : '' }}'; // Enviar el ID del joven como filtro
                        d.periodo = $('#filtroPeriodo').val(); // Agregar el valor del filtro de período
                        d.predefinido = $('#filtroPredefinido').val();
                        // Agrega otros parámetros si es necesario
                        // d.otroParametro = valor;
                    }
                },
                columns: [

                    {data: 'persona_nombre', name: 'persona_nombre', visible:false},
                    {data: 'periodo', name: 'periodo'},

                    {data: 'persona_apellido', name: 'persona_apellido'},
                    {
                        data: 'fecha',
                        name: 'fecha',
                        render: function(data) {
                            // Verificar si el dato es válido
                            if (data) {
                                // Formatear la fecha y hora en DD/MM/YYYY HH:mm:ss
                                return moment(data).format('DD/MM/YYYY HH:mm:ss');
                            }
                            // Si no hay datos, retornar un valor por defecto o una cadena vacía
                            return '';
                        }
                    },
                    {
                        data: 'usuario_nombre', // Acceder al nombre de la usuario a través de la relación
                        name: 'usuario_nombre',
                        orderable: true,
                        searchable: true
                    },

                    {data: 'interno', name: 'interno'},
                    {data: 'estado', name: 'estado'},
                    {data: 'puntaje', name: 'puntaje'},
                    {
                        "data": "id",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, row) {
                            // Construir HTML para las acciones
                            //console.log(data, row);
                            var actionsHtml = '';

                            @can('joven_evaluacion_estado-listar')
                                actionsHtml += '<a href="{{ route("joven_evaluacion_estados.index") }}?joven_evaluacion_id=' + row.id + '" alt="Estados" title="Estados" style="margin-right: 5px;"><i class="fa fa-tasks"></i></a>';
                            @endcan
                            @can('evaluacion-listar')
                                if ((row.estado == 'En evaluación')||(row.estado == 'Evaluada')||(row.estado == 'Rectificada')) {
                                    actionsHtml += '<a href="{{ route("joven_evaluacions.evaluacion-pdf") }}?joven_evaluacion_id=' + row.id + '" alt="Evaluación" title="Evaluación" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';
                                }
                            @endcan


                            return actionsHtml;

                        },
                }
                ],
                "language": {
                    "url": "{{ asset('bower_components/datatables.net/lang/es-AR.json') }}"
                },
                "initComplete": function() {
                    // Recuperar el valor del filtro desde la sesión
                    var filtroGuardado = '{{ session('nombre_filtro_joven_evaluacion', '') }}';

                    // Establecer el valor en el input de búsqueda
                    if (filtroGuardado) {
                        $('#example1_filter input[type="search"]').val(filtroGuardado);
                    }

                    // Agregar botón "Limpiar Filtro" justo después del input de búsqueda
                    $('#example1_filter').append('<button id="clearFilter" class="btn btn-secondary btn-sm" style="margin-left: 10px;">Limpiar Filtro</button>');

                    // Asignar acción al botón "Limpiar Filtro"
                    $('#clearFilter').click(function() {
                        // Enviar una solicitud al servidor para limpiar la sesión
                        $.post("{{ route('joven_evaluacions.clearFilter') }}", {
                            _token: '{{ csrf_token() }}'
                        })
                            .done(function(response) {
                                // Limpiar el input de búsqueda
                                $('#example1_filter input[type="search"]').val('');

                                // Hacer la búsqueda en la tabla (esto limpiará los resultados filtrados)
                                table.search('').draw();

                                //console.log('Filtro limpiado y tabla redibujada');
                            })
                            .fail(function(xhr) {
                                console.error('Error al limpiar el filtro:', xhr.responseText);
                            });
                    });
                }
            });
            // Evento para manejar el cambio en el filtro de período
            $('#filtroPeriodo').change(function() {
                table.ajax.reload(); // Recargar la tabla cuando cambie el filtro de período
            });
            $('#filtroPredefinido').change(function() {
                table.ajax.reload(); // Recargar la tabla cuando cambie el filtro de período
            });
        });
        function confirmarEnvio() {
            return confirm('¿Está seguro?');
        }
    </script>
@endsection
