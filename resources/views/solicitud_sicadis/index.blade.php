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
                <i class="fa fa-layer-group" aria-hidden="true"></i>Solicitudes SICADI
                <!--<small>Create, Read, Update, Delete</small>-->
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('solicitud_sicadis.index') }}">Solicitudes</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            @can('solicitud-rechazar')
                                @if (session('selected_rol') == 1)
                                    <a class='pull-right btn btn-info' href="{{ route('solicitud_sicadis.importar') }}" style="margin-left: 5px">Importar</a>
                                @endif
                            @endcan
                                @can('solicitud-rechazar')
                                    <a class='pull-right btn btn-info' style="margin-left: 5px;" id="exportar-datos" href="#">Exportar</a>
                                @endcan
                            @can('solicitud_sicadi-crear')
                                <a class='pull-right btn btn-success' href="{{ route('solicitud_sicadis.create') }}">Nueva</a>
                            @endcan
                        </div>
                        @include('includes.messages')

                        @php
                            $currentYear = date('Y');
                        @endphp
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="filtroYear">Año:</label>
                                        <select id="filtroYear" class="form-control" style="width: 100%">
                                            <option value="-1">Todos</option>
                                            @for ($year = 2024; $year <= $currentYear; $year++)
                                                <option value="{{ $year }}"
                                                    {{ $year == $currentAnio ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        {{Form::label('tipo', 'Convocatoria')}}
                                        {{ Form::select('tipo',['-1'=>'Todas']+ config('convocatorias'), '',['class' => 'form-control']) }}

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {{Form::label('mecanismo', 'Mecanismo')}}
                                        {{ Form::select('mecanismo',['-1'=>'Todos']+ config('mecanismos'), '',['class' => 'form-control']) }}

                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        {{Form::label('solicitada', 'Solicitada')}}
                                        {{ Form::select('solicitada',['-1'=>'Todas']+ config('categorias'), '',['class' => 'form-control']) }}

                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        {{Form::label('estado', 'Estado')}}
                                        {{ Form::select('estado',['-1'=>'Todos']+ config('estados'), '',['class' => 'form-control']) }}

                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        {{Form::label('presentacion_ua', 'UA')}}
                                        {{ Form::select('presentacion_ua',['-1'=>'Todas']+ config('facultades'), '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%']) }}



                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        {{Form::label('asignada', 'Asignada')}}
                                        {{ Form::select('asignada',['-1'=>'Todas']+ config('categorias'), '',['class' => 'form-control']) }}

                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        {{Form::label('otorgadas', 'Otorgadas')}}<br>
                                        {{Form::checkbox('otorgadas', 1,false)}}


                                    </div>
                                </div>
                            </div>
                            <!-- /.form-group -->
                        </div>                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <!--<th>Nro.</th>-->

                                    <th>Investigador</th>

                                    <th>Investigador/a</th>
                                    <th>Cuil</th>
                                    <th>Fecha</th>
                                    <th>U. Académica</th>
                                    <th>Convocatoria</th>
                                    <th>Estado</th>

                                    <th>Solicitada</th>
                                    <th>Asignada</th>


                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <!--<th>Nro.</th>-->

                                    <th>Investigador</th>

                                    <th>Investigador/a</th>
                                    <th>Cuil</th>
                                    <th>Fecha</th>
                                    <th>U. Académica</th>
                                    <th>Convocatoria</th>
                                    <th>Estado</th>

                                    <th>Solicitada</th>
                                    <th>Asignada</th>


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
                "order": [[ 1, "asc" ]], // Ordenar por el tercer campo (apellido) de forma ascendente
                "ajax": {
                    "url": "{{ route('solicitud_sicadis.dataTable') }}",
                    "type": "POST",
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}'; // Agrega el token CSRF si estás usando Laravel
                        d.filtroYear = $('#filtroYear').val(); // Agregar el valor del filtro de período
                        d.tipo = $('#tipo').val();
                        d.mecanismo = $('#mecanismo').val();
                        d.solicitada = $('#solicitada').val();

                        d.estado = $('#estado').val();


                        d.presentacion_ua = $('#presentacion_ua').val();
                        d.asignada = $('#asignada').val();
                        d.otorgadas = $('#otorgadas').is(':checked') ? 1 : 0;
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

                    {data: 'persona_apellido', name: 'persona_apellido'},
                    {data: 'cuil', name: 'cuil'},
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
                    {data: 'presentacion_ua', name: 'presentacion_ua'},
                    {data: 'convocatoria', name: 'convocatoria'},
                    {data: 'estado', name: 'estado'},

                    {data: 'categoria_solicitada', name: 'categoria_solicitada'},
                    {data: 'categoria_asignada', name: 'categoria_asignada'},




                    {
                        "data": "id",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, row) {
                            // Construir HTML para las acciones
                            var actionsHtml = '';
                            @can('solicitud_sicadi_estado-listar')
                                actionsHtml += '<a href="{{ route("solicitud_sicadi_estados.index") }}?solicitud_sicadi_id=' + row.id + '" alt="Estados" title="Estados" style="margin-right: 5px;"><i class="fa fa-tasks"></i></a>';
                            @endcan
                                @if (session('selected_rol') == 2)
                                if (row.estado == 'Creada') {
                                    @can('solicitud_sicadi-editar')
                                        actionsHtml += '<a href="{{ route("solicitud_sicadis.edit", ":id") }}" alt="Editar" title="Editar"><span class="glyphicon glyphicon-edit"></span></a>'.replace(':id', row.id);
                                    @endcan
                                        @can('solicitud_sicadi-eliminar')
                                        actionsHtml += '<form id="delete-form-' + row.id + '" method="post" action="{{ route('solicitud_sicadis.destroy', '') }}/' + row.id + '" style="display: none">';
                                        actionsHtml += '{{ csrf_field() }}';
                                        actionsHtml += '{{ method_field('DELETE') }}';
                                        actionsHtml += '</form>';
                                        actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'delete-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Eliminar" title="Eliminar" style="margin-right: 5px;"><span class="glyphicon glyphicon-trash"></span></a>';
                                    @endcan
                                }
                            @else
                                @can('solicitud_sicadi-editar')
                                    actionsHtml += '<a href="{{ route("solicitud_sicadis.edit", ":id") }}" alt="Editar" title="Editar"><span class="glyphicon glyphicon-edit"></span></a>'.replace(':id', row.id);
                                @endcan

                                @can('solicitud_sicadi-eliminar')
                                actionsHtml += '<form id="delete-form-' + row.id + '" method="post" action="{{ route('solicitud_sicadis.destroy', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';
                                actionsHtml += '{{ method_field('DELETE') }}';
                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'delete-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Eliminar" title="Eliminar" style="margin-right: 5px;"><span class="glyphicon glyphicon-trash"></span></a>';
                                @endcan
                            @endif


                            // Agregar enlace para descargar el PDF del integrante
                            actionsHtml += '<a href="{{ route("solicitud_sicadis.solicitud-pdf") }}?solicitud_sicadi_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';

                            actionsHtml += '<a href="{{ route("solicitud_sicadis.archivos") }}?solicitud_sicadi_id=' + row.id + '" alt="Descargar archivos" title="Descargar archivos" target="_blank" style="margin-right: 5px;"><i class="fa fa-download"></i></a>';


                            if (row.estado == 'Creada') {
                                @can('solicitud-editar')

                                    actionsHtml += '<form id="send-form-' + row.id + '" method="post" action="{{ route('solicitud_sicadis.enviar', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'send-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar" title="Enviar" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                @endcan
                            }
                            if (row.estado == 'Recibida') {
                                @can('solicitud-admitir')
                                    actionsHtml += '<form id="admit-form-' + row.id + '" method="post" action="{{ route('solicitud_sicadis.admitir', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admit-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir" title="Admitir" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                @endcan
                                    @can('solicitud-rechazar')
                                    actionsHtml += '<a href="{{ route("solicitud_sicadis.rectificar", ":id") }}" alt="Rectificar" title="Rectificar" style="margin-right: 5px;"><i class="fa fa-pencil-alt"></i></a>'.replace(':id', row.id);
                                @endcan
                                    @can('solicitud-rechazar')
                                    actionsHtml += '<a href="{{ route("solicitud_sicadis.rechazar", ":id") }}" alt="Rechazar" title="Rechazar" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
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
                // Guardar y restaurar el filtro externo
                stateSaveParams: function (settings, data) {
                    data.filtroYear = $('#filtroYear').val();
                    data.estado = $('#estado').val();
                    data.tipo = $('#tipo').val();
                    data.mecanismo = $('#mecanismo').val();
                    data.solicitad = $('#solicitad').val();
                    data.presentacion_ua = $('#presentacion_ua').val();
                    data.asignada = $('#asignada').val();
                    data.otorgadas = $('#otorgadas').val();
                },
                stateLoadParams: function (settings, data) {
                    if (data.filtroYear) {
                        $('#filtroYear').val(data.filtroYear).trigger('change');
                    }
                    if (data.estado) {
                        $('#estado').val(data.estado).trigger('change');
                    }
                    if (data.tipo) {
                        $('#tipo').val(data.tipo).trigger('change');
                    }
                    if (data.mecanismo) {
                        $('#mecanismo').val(data.mecanismo).trigger('change');
                    }
                    if (data.solicitad) {
                        $('#solicitad').val(data.solicitad).trigger('change');
                    }
                    if (data.presentacion_ua) {
                        $('#presentacion_ua').val(data.presentacion_ua).trigger('change');
                    }
                    if (data.asignada) {
                        $('#asignada').val(data.asignada).trigger('change');
                    }
                    if (data.otorgadas) {
                        $('#otorgadas').val(data.otorgadas).trigger('change');
                    }
                },
                "initComplete": function() {

                }
            });
            // Evento para manejar el cambio en el filtro de período
            $('#filtroYear').change(function() {
               table.ajax.reload(null, false);
            });
            $('#tipo').change(function() {
               table.ajax.reload(null, false);
            });
            $('#mecanismo').change(function() {
               table.ajax.reload(null, false);
            });
            $('#solicitada').change(function() {
               table.ajax.reload(null, false);
            });
            $('#estado').change(function() {
               table.ajax.reload(null, false);
            });

            $('#presentacion_ua').change(function() {
               table.ajax.reload(null, false);
            });
            $('#asignada').change(function() {
               table.ajax.reload(null, false);
            });
            $('#otorgadas').click(function() {
               table.ajax.reload(null, false);
            });

            $('#exportar-datos').click(function () {
                // Recoge los filtros actuales del DataTable
                var filtros = {
                    filtroYear: $('#filtroYear').val(),
                    tipo: $('#tipo').val(),
                    mecanismo: $('#mecanismo').val(),
                    solicitada: $('#solicitada').val(),
                    estado: $('#estado').val(),

                    presentacion_ua: $('#presentacion_ua').val(),
                    asignada: $('#asignada').val(),
                    otorgada: $('#otorgada').val(),
                    // Captura el valor del filtro nativo del DataTable
                    busqueda: $('input[type="search"]').val() // Ajusta el selector según tu configuración

                };

                // Realiza una solicitud GET para exportar los datos
                window.location.href = '{{ route('solicitud_sicadis.exportar') }}?' + $.param(filtros);
            });

        });

    </script>
@endsection
