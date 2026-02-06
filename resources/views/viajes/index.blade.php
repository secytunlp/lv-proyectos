@extends('layouts.app')

@section('headSection')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.css') }}">
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
                <i class="fa fa-plane" aria-hidden="true"></i>Viajes/Estadías
                <!--<small>Create, Read, Update, Delete</small>-->
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('viajes.index') }}">Viajes/Estadías</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Solicitud</h3>
                            @can('solicitud-rechazar')
                                <a class='pull-right btn btn-info' style="margin-left: 5px;" id="exportar-datos" href="#">Exportar</a>
                            @endcan
                            <a class='pull-right btn btn-success' href="{{ route('viajes.create') }}">Nueva</a>

                        </div>
                        @include('includes.messages')

                        <!-- Filtro de Período -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="filtroPeriodo">Período:</label>
                                        <select id="filtroPeriodo" class="form-control">
                                            <option value="-1">Todos</option>
                                            @foreach($periodos as $periodo)
                                                <option value="{{ $periodo->id }}"
                                                    {{ $periodo->id == $currentPeriod ? 'selected' : '' }}>
                                                    {{ $periodo->nombre }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        {{Form::label('estado', 'Estado')}}
                                            {{ Form::select('estado',['-1'=>'Todos','Creada'=>'Creada','Recibida'=>'Recibida','Admitida'=>'Admitida','No Admitida'=>'No Admitida','Otorgada-No-Rendida'=>'Otorgada-No-Rendida','En evaluación'=>'En evaluación','No otorgada'=>'No otorgada','Evaluada'=>'Evaluada','Otorgada-Rendida'=>'Otorgada-Rendida','Otorgada-Renunciada'=>'Otorgada-Renunciada','Retirada'=>'Retirada','Otorgada-Devuelta'=>'Otorgada-Devuelta'], '',['class' => 'form-control']) }}

                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        {{Form::label('area', 'Área')}}
                                        {{ Form::select('area',['-1'=>'Todas','Exactas'=>'Exactas','Naturales'=>'Naturales','Sociales'=>'Sociales'], '',['class' => 'form-control']) }}

                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        {{Form::label('facultadplanilla', 'U. Académica')}}
                                        {{Form::select('facultadplanilla_id',  $facultades,'', ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'facultadplanilla_id'])}}

                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        {{Form::label('motivo', 'Motivo')}}
                                        {{ Form::select('motivo',['-1'=>'Todos','A) Reuniones Científicas'=>'A','B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP'=>'B','C) Estadía de Trabajo en la UNLP para un Investigador Invitado'=>'C'], '',['class' => 'form-control']) }}


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
                        </div>
                        <!-- /.box-body -->
                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped table-hover">
                                @php
                                    // Definir los roles permitidos
                                    $rolesPermitidos = [1, 4, 5, 7]; // Agrega aquí los roles permitidos
                                @endphp
                                <thead>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Investigador</th>

                                    <th>Período</th>
                                    <th>Solicitante</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Área</th>
                                    <th>U. Académica</th>
                                    <th>Motivo</th>
                                    <th>Monto</th>
                                    <th>Lugar</th>
                                    @if (session('selected_rol') == 6) <!-- Verifica si el usuario es evaluador -->
                                    <th>Estado de Evaluación</th>
                                    @endif
                                    @if (in_array(session('selected_rol'), $rolesPermitidos))
                                        <!-- Verifica si el usuario tiene un rol permitido -->
                                        <th>Evaluadores</th>
                                        <th>Diferencia</th>
                                        <th>Puntaje</th>
                                    @endif
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Investigador</th>

                                    <th>Período</th>
                                    <th>Solicitante</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Área</th>
                                    <th>U. Académica</th>
                                    <th>Motivo</th>
                                    <th>Monto</th>
                                    <th>Lugar</th>
                                    @if (session('selected_rol') == 6) <!-- Verifica si el usuario es evaluador -->
                                    <th>Estado de Evaluación</th>
                                    @endif
                                    @if (in_array(session('selected_rol'), $rolesPermitidos))
                                        <!-- Verifica si el usuario tiene un rol permitido -->
                                        <th>Evaluadores</th>
                                        <th>Diferencia</th>
                                        <th>Puntaje</th>
                                    @endif
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
    <!-- Select2 -->
    <script src="{{ asset('bower_components/select2/dist/js/select2.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- page script -->
    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
            // Verificar si el usuario es evaluador
            var isEvaluador = {{ session('selected_rol') == 6 ? 'true' : 'false' }};

            // Definir los roles permitidos para ver puntajes
            var rolesPermitidos = {!! json_encode([1, 4, 5, 7]) !!}; // Esto convierte el array de PHP a JavaScript de forma segura
            var selectedRol = {{ session('selected_rol') }};
            var verPuntaje = rolesPermitidos.includes(selectedRol);
            let columns = [
                {data: 'persona_nombre', name: 'persona_nombre', visible:false},
                {
                    data: 'periodo_nombre', // Acceder al nombre de la periodo a través de la relación
                    name: 'periodo_nombre',
                    orderable: true,
                    searchable: true
                },
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
                {data: 'estado', name: 'estado'},

                {
                    data: 'facultad_cat', // Acceder al nombre de la facultad a través de la relación
                    name: 'facultad_cat',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'facultad_nombre', // Acceder al nombre de la facultad a través de la relación
                    name: 'facultad_nombre',
                    orderable: true,
                    searchable: true
                },
                {data: 'motivo', name: 'motivo'},
                {data: 'monto', name: 'monto'},
                {
                    data: 'ambitos', // Asegúrate de que este campo esté en los datos que devuelve tu servidor
                    name: 'ambitos',
                    visible: true, // O puedes omitir esta línea porque es visible por defecto
                    orderable: false,
                    render: function (data) {
                        // Asegúrate de que 'data' es un array de strings
                        return data.join('<br>'); // Usa <br> para saltos de línea, o puedes usar ', ' para comas
                    }
                }
            ]

            let actionsColumn = {

                    "data": "id",
                    "orderable": false,
                    "searchable": false,
                    "render": function (data, type, row) {
                        // Construir HTML para las acciones
                        var actionsHtml = '';

                        @can('joven_estado-listar')
                            actionsHtml += '<a href="{{ route("viaje_estados.index") }}?viaje_id=' + row.id + '" alt="Estados" title="Estados" style="margin-right: 5px;"><i class="fa fa-tasks"></i></a>';
                        @endcan
                        if (row.estado == 'Creada') {
                            @can('solicitud-editar')
                                actionsHtml += '<a href="{{ route("viajes.edit", ":id") }}" alt="Modificar solicitud" title="Modificar solicitud" style="margin-right: 5px;"><span class="glyphicon glyphicon-edit"></span></a>'.replace(':id', row.id);
                            @endcan
                            // Agregar formulario de eliminación si el usuario tiene permiso
                            @can('solicitud-eliminar')
                                actionsHtml += '<form id="delete-form-' + row.id + '" method="post" action="{{ route('viajes.destroy', '') }}/' + row.id + '" style="display: none">';
                            actionsHtml += '{{ csrf_field() }}';
                            actionsHtml += '{{ method_field('DELETE') }}';
                            actionsHtml += '</form>';
                            actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'delete-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Eliminar" title="Eliminar" style="margin-right: 5px;"><span class="glyphicon glyphicon-trash"></span></a>';
                            @endcan
                        }


                        // Agregar enlace para descargar el PDF del integrante
                        actionsHtml += '<a href="{{ route("viajes.solicitud-pdf") }}?viaje_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';

                        actionsHtml += '<a href="{{ route("viajes.archivos") }}?viaje_id=' + row.id + '" alt="Descargar archivos" title="Descargar archivos" target="_blank" style="margin-right: 5px;"><i class="fa fa-download"></i></a>';


                        if (row.estado == 'Creada') {

                            @can('solicitud-editar')
                                actionsHtml += '<form id="send-form-' + row.id + '" method="post" action="{{ route('viajes.enviar', '') }}/' + row.id + '" style="display: none">';
                            actionsHtml += '{{ csrf_field() }}';

                            actionsHtml += '</form>';
                            actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'send-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar" title="Enviar" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                            @endcan
                        }
                        if (row.estado == 'Recibida') {
                            @can('solicitud-admitir')
                                actionsHtml += '<form id="admit-form-' + row.id + '" method="post" action="{{ route('viajes.admitir', '') }}/' + row.id + '" style="display: none">';
                            actionsHtml += '{{ csrf_field() }}';

                            actionsHtml += '</form>';
                            actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admit-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir" title="Admitir" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                            @endcan
                                @can('solicitud-rechazar')
                                actionsHtml += '<a href="{{ route("viajes.rechazar", ":id") }}" alt="Rechazar" title="Rechazar" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                            @endcan
                        }

                        if ((row.estado == 'Admitida') || (row.estado == 'Otorgada-No-Rendida') || (row.estado == 'En evaluación') || (row.estado == 'No otorgada') || (row.estado == 'Evaluada') || (row.estado == 'Otorgada-Rendida') || (row.estado == 'Otorgada-Renunciada') || (row.estado == 'Otorgada-Devuelta')) {
                            @can('evaluacion-listar')
                                actionsHtml += '<a href="{{ route("viaje_evaluacions.index") }}?viaje_id=' + row.id + '" alt="Evaluaciones" title="Evaluaciones" style="margin-right: 5px;"><i class="fa fa-th-list"></i></a>';
                            @endcan
                        }
                        if (row.estado == 'En evaluación') {
                            if (row.evaluacion_estado == 'Recibida') {
                                @can('evaluacion-evaluar')
                                    actionsHtml += '<form id="accept-form-' + row.id + '" method="post" action="{{ route('viaje_evaluacions.aceptar', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'accept-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Aceptar" title="Aceptar" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                actionsHtml += '<a href="{{ route("viaje_evaluacions.rechazar", ":id") }}" alt="Rechazar" title="Rechazar" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                                @endcan
                            }
                            if (row.evaluacion_estado == 'En evaluación') {
                                @can('evaluacion-evaluar')

                                    actionsHtml += '<a href="{{ route("viaje_evaluacions.evaluar", ":id") }}" alt="Evaluar" title="Evaluar" style="margin-right: 5px;"><i class="fa fa-clipboard-check"></i></a>'.replace(':id', row.id);

                                    actionsHtml += '<form id="sendEval-form-' + row.id + '" method="post" action="{{ route('viaje_evaluacions.send', '') }}/' + row.id + '" style="display: none">';
                                    actionsHtml += '{{ csrf_field() }}';
                                    actionsHtml += '</form>';
                                    actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la evaluación no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'sendEval-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar" title="Enviar" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                @endcan
                            }
                        }
                        if (row.evaluacion_estado == 'En evaluación' || row.evaluacion_estado == 'Evaluada') {
                            @can('evaluacion-evaluar')

                                actionsHtml += '<a href="{{ route("viaje_evaluacions.evaluacion-pdf") }}?viaje_id=' + row.id + '" alt="Evaluación" title="Evaluación" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';
                            @endcan
                        }
                        return actionsHtml;

                    },
                }
            ;

            // Agregar la columna 'evaluacion_estado' solo si el rol es evaluador
            if (isEvaluador) {
                columns.push({
                    data: 'evaluacion_estado',
                    name: 'evaluacion_estado',
                    visible: true // O puedes omitir esta línea porque es visible por defecto
                });
            }

            if (verPuntaje) {
                // Agregar columna para los evaluadores
                columns.push({
                    data: 'evaluadores', // Asegúrate de que este campo esté en los datos que devuelve tu servidor
                    name: 'evaluadores',
                    visible: true, // O puedes omitir esta línea porque es visible por defecto
                    orderable: false,
                    render: function(data) {
                        // Asegúrate de que 'data' es un array de strings
                        return data.join('<br>'); // Usa <br> para saltos de línea, o puedes usar ', ' para comas
                    }
                });
                columns.push({
                    data: 'diferencia',
                    name: 'diferencia',
                    visible: true // O puedes omitir esta línea porque es visible por defecto
                });
                columns.push({
                    data: 'puntaje',
                    name: 'puntaje',
                    visible: true // O puedes omitir esta línea porque es visible por defecto

                });
            }

            // Asegurarte de que la columna de acciones esté al final
            columns.push(actionsColumn);

            var table = $('#example1').DataTable({
                "processing": true, // Activar la indicación de procesamiento
                "serverSide": true, // Habilitar el procesamiento del lado del servidor
                "autoWidth": false, // Desactiva el ajuste automático del anchos
                responsive: true,
                scrollX: true,
                paging : true,
                "order": [[ 2, "asc" ]], // Ordenar por el tercer campo (apellido) de forma ascendente
                "ajax": {
                    "url": "{{ route('viajes.dataTable') }}",
                    "type": "POST",
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}'; // Agrega el token CSRF si estás usando Laravel
                        d.periodo = $('#filtroPeriodo').val(); // Agregar el valor del filtro de período
                        d.estado = $('#estado').val();
                        d.area = $('#area').val();
                        d.facultadplanilla = $('#facultadplanilla_id').val();
                        d.motivo = $('#motivo').val();
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
                columns: columns,
                "language": {
                    "url": "{{ asset('bower_components/datatables.net/lang/es-AR.json') }}"
                },
                stateSave: true,
                // Guardar y restaurar el filtro externo
                stateSaveParams: function (settings, data) {
                    data.filtroPeriodo = $('#filtroPeriodo').val();
                    data.estado = $('#estado').val();
                    data.area = $('#area').val();
                    data.facultadplanilla_id = $('#facultadplanilla_id').val();
                    data.motivo = $('#motivo').val();
                    data.otorgadas = $('#otorgadas').val();
                },
                stateLoadParams: function (settings, data) {
                    if (data.filtroPeriodo) {
                        $('#filtroPeriodo').val(data.filtroPeriodo).trigger('change');
                    }
                    if (data.estado) {
                        $('#estado').val(data.estado).trigger('change');
                    }
                    if (data.area) {
                        $('#area').val(data.area).trigger('change');
                    }
                    if (data.facultadplanilla_id) {
                        $('#facultadplanilla_id').val(data.facultadplanilla_id).trigger('change');
                    }
                    if (data.otorgadas) {
                        $('#otorgadas').val(data.otorgadas).trigger('change');
                    }
                    if (data.motivo) {
                        $('#motivo').val(data.motivo).trigger('change');
                    }
                },
                "initComplete": function() {

                }
            });
            // Evento para manejar el cambio en el filtro de período
            $('#filtroPeriodo').change(function() {
                table.ajax.reload(); // Recargar la tabla cuando cambie el filtro de período
            });
            $('#estado').change(function() {
                table.ajax.reload(); // Recargar la tabla cuando cambie el filtro de período
            });
            $('#area').change(function() {
                table.ajax.reload(); // Recargar la tabla cuando cambie el filtro de período
            });
            $('#facultadplanilla_id').change(function() {
                table.ajax.reload(); // Recargar la tabla cuando cambie el filtro de período
            });
            $('#motivo').change(function() {
                table.ajax.reload(); // Recargar la tabla cuando cambie el filtro de período
            });
            $('#otorgadas').click(function() {
                table.ajax.reload(); // Recargar la tabla cuando cambie el filtro de período
            });

            $('#exportar-datos').click(function () {
                // Recoge los filtros actuales del DataTable
                var filtros = {
                    periodo: $('#filtroPeriodo').val(),
                    estado: $('#estado').val(),
                    area: $('#area').val(),
                    facultadplanilla: $('#facultadplanilla_id').val(),
                    motivo: $('#motivo').val(),
                    otorgadas: $('#otorgadas').is(':checked') ? 1 : 0,
                    // Captura el valor del filtro nativo del DataTable
                    busqueda: $('input[type="search"]').val() // Ajusta el selector según tu configuración

                };

                // Realiza una solicitud GET para exportar los datos
                window.location.href = '{{ route('viajes.exportar') }}?' + $.param(filtros);
            });
        });

    </script>
@endsection
