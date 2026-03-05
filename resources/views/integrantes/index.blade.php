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
                <i class="fa fa-user-friends" aria-hidden="true"></i> Integrantes

            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('integrantes.index') }}">Integrantes</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Integrantes @if($proyecto) - {{ $proyecto->codigo }}@endif</h3>
                            @if($proyecto)<a class='pull-right btn btn-success' href="{{ route('integrantes.create', ['proyecto_id' => $proyecto->id]) }}">Nuevo</a>@endif
                        </div>
                        @include('includes.messages')


                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Investigador</th>
                                    <th>Proyecto</th>
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
                                    <th>Organismo</th>-->
                                    <th>Alta</th>
                                    <th>Baja</th>
                                    <th>U. Académica</th>
                                    <th>Horas</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Investigador</th>
                                    <th>Proyecto</th>
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
                                    <th>Organismo</th>-->
                                    <th>Alta</th>
                                    <th>Baja</th>
                                    <th>U. Académica</th>
                                    <th>Horas</th>
                                    <th>Estado</th>
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
                    "url": "{{ route('integrantes.dataTable') }}",
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

                    {data: 'persona_nombre', name: 'persona_nombre', visible:false},
                    {data: 'codigo', name: 'codigo'},
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
                    },*/
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
                    },

                    {
                        data: 'facultad_nombre', // Acceder al nombre de la facultad a través de la relación
                        name: 'facultad_nombre',
                        orderable: true,
                        searchable: true
                    },

                    {data: 'horas', name: 'horas'},


                    {data: 'estado', name: 'estado'},
                    {
                        "data": "id",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, row) {
                            // Construir HTML para las acciones

                            var actionsHtml = '';

// Agregar enlace de edición si el usuario tiene permiso
                            @can('integrante-listar')
                                actionsHtml += '<a href="{{ route("integrantes.show", ":id") }}" alt="Ver integrante" title="Ver integrante" style="margin-right: 5px;"><span class="glyphicon glyphicon-eye-open"></span></a>'.replace(':id', row.id);
                            @endcan
                            @can('integrante_estado-listar')
                                actionsHtml += '<a href="{{ route("integrante_estados.index") }}?integrante_id=' + row.id + '" alt="Estados" title="Estados" style="margin-right: 5px;"><i class="fa fa-tasks"></i></a>';
                            @endcan




                            //console.log(row.estado);
                            if(row.estado == 'Alta Creada') {
                                @can('integrante-editar')
                                    actionsHtml += '<a href="{{ route("integrantes.edit", ":id") }}" alt="Modificar alta" title="Modificar alta" style="margin-right: 5px;"><span class="glyphicon glyphicon-edit"></span></a>'.replace(':id', row.id);
                                @endcan
                                // Agregar formulario de eliminación si el usuario tiene permiso
                                @can('integrante-eliminar')
                                    actionsHtml += '<form id="delete-form-' + row.id + '" method="post" action="{{ route('integrantes.destroy', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';
                                actionsHtml += '{{ method_field('DELETE') }}';
                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'delete-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Anular alta" title="Anular alta" style="margin-right: 5px;"><span class="glyphicon glyphicon-trash"></span></a>';
                                @endcan
                            }

                            if(((row.estado == '')||(row.estado == null)) &&(row.baja == null)&&(row.tipo != 'Director')&&(row.tipo != 'Codirector')){
                                @can('integrante-eliminar')
                                    actionsHtml += '<a href="{{ route("integrantes.baja", ":id") }}" alt="Baja de integrante" title="Baja de integrante" style="margin-right: 5px;"><span class="glyphicon glyphicon-remove"></span></a>'.replace(':id', row.id);
                                @endcan

                            }
                            if(row.estado == 'Baja Creada') {
                                @can('integrante-eliminar')
                                    actionsHtml += '<form id="anular-form-' + row.id + '" method="post" action="{{ route('integrantes.anular', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'anular-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Anular Baja" title="Anular Baja" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>';
                                @endcan

                            }
                            if(((row.estado == '')||(row.estado == null)) &&(row.baja == null) &&(row.tipo == 'Colaborador')){
                                @can('integrante-editar')
                                    actionsHtml += '<a href="{{ route("integrantes.cambio", ":id") }}" alt="Cambio de colaborador a integrante" title="Cambio de colaborador a integrante" style="margin-right: 5px;"><span class="glyphicon glyphicon-refresh"></span></a>'.replace(':id', row.id);
                                @endcan

                            }
                            if(((row.estado == '')||(row.estado == null)) &&(row.baja == null) &&(row.tipo != 'Colaborador')){
                                @can('integrante-editar')
                                    actionsHtml += '<a href="{{ route("integrantes.cambioHS", ":id") }}" alt="Cambio de dedicación horaria" title="Cambio de dedicación horaria" style="margin-right: 5px;"><i class="fa fa-clock"></i></a>'.replace(':id', row.id);
                                @endcan

                            }
                            if(((row.estado == '')||(row.estado == null)) &&(row.baja == null)&&(row.tipo != 'Director')&&(row.tipo != 'Codirector')&&(row.tipo != 'Colaborador')){
                                @can('integrante-editar')
                                    actionsHtml += '<a href="{{ route("integrantes.cambioTipo", ":id") }}" alt="Cambio de tipo de integrante" title="Cambio de tipo de integrante" style="margin-right: 5px;"><span class="glyphicon glyphicon-refresh"></span></a>'.replace(':id', row.id);
                                @endcan

                            }
                            if(row.estado == 'Cambio Creado') {
                                @can('integrante-editar')
                                    actionsHtml += '<a href="{{ route("integrantes.cambio", ":id") }}" alt="Cambio de colaborador a integrante" title="Cambio de colaborador a integrante" style="margin-right: 5px;"><span class="glyphicon glyphicon-refresh"></span></a>'.replace(':id', row.id);
                                actionsHtml += '<form id="anularCambio-form-' + row.id + '" method="post" action="{{ route('integrantes.anularCambio', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'anularCambio-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Anular Cambio de colaborador a integrante" title="Anular Cambio de colaborador a integrante" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>';
                                @endcan

                            }
                            if(row.estado == 'Cambio Hs. Creado') {
                                @can('integrante-editar')
                                    actionsHtml += '<a href="{{ route("integrantes.cambioHS", ":id") }}" alt="Cambio de dedicación horaria" title="Cambio de dedicación horaria" style="margin-right: 5px;"><span class="fa fa-clock"></span></a>'.replace(':id', row.id);
                                    actionsHtml += '<form id="anularHS-form-' + row.id + '" method="post" action="{{ route('integrantes.anularHS', '') }}/' + row.id + '" style="display: none">';
                                    actionsHtml += '{{ csrf_field() }}';

                                    actionsHtml += '</form>';
                                    actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'anularHS-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Anular Cambio de dedicación horaria" title="Anular Cambio de dedicación horaria" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>';
                                @endcan

                            }
                            if(row.estado == 'Cambio Tipo Creado') {
                                @can('integrante-editar')

                                actionsHtml += '<form id="anularTipo-form-' + row.id + '" method="post" action="{{ route('integrantes.anularTipo', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'anularTipo-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Anular Cambio de tipo" title="Anular Cambio de tipo" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>';
                                @endcan

                            }

                            if((row.estado == 'Alta Recibida')||(row.estado == 'Alta Creada')) {

                                // Agregar enlace para descargar el PDF del integrante
                                actionsHtml += '<a href="{{ route("integrantes.alta-pdf") }}?integrante_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';

                                actionsHtml += '<a href="{{ route("integrantes.archivos") }}?integrante_id=' + row.id + '" alt="Descargar archivos" title="Descargar archivos" target="_blank" style="margin-right: 5px;"><i class="fa fa-download"></i></a>';




                            }
                            if((row.estado == 'Baja Recibida')||(row.estado == 'Baja Creada')) {

                                // Agregar enlace para descargar el PDF del integrante
                                actionsHtml += '<a href="{{ route("integrantes.baja-pdf") }}?integrante_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';






                            }
                            if((row.estado == 'Cambio Recibido')||(row.estado == 'Cambio Creado')) {

                                // Agregar enlace para descargar el PDF del integrante
                                actionsHtml += '<a href="{{ route("integrantes.cambio-pdf") }}?integrante_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';

                                actionsHtml += '<a href="{{ route("integrantes.archivos") }}?integrante_id=' + row.id + '" alt="Descargar archivos" title="Descargar archivos" target="_blank" style="margin-right: 5px;"><i class="fa fa-download"></i></a>';




                            }
                            if((row.estado == 'Cambio Hs. Recibido')||(row.estado == 'Cambio Hs. Creado')) {

                                // Agregar enlace para descargar el PDF del integrante
                                actionsHtml += '<a href="{{ route("integrantes.cambioHS-pdf") }}?integrante_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';


                                actionsHtml += '<a href="{{ route("integrantes.archivos") }}?integrante_id=' + row.id + '" alt="Descargar archivos" title="Descargar archivos" target="_blank" style="margin-right: 5px;"><i class="fa fa-download"></i></a>';



                            }
                            if((row.estado == 'Cambio Tipo Recibido')||(row.estado == 'Cambio Tipo Creado')) {

                                // Agregar enlace para descargar el PDF del integrante
                                actionsHtml += '<a href="{{ route("integrantes.cambioTipo-pdf") }}?integrante_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';

                                actionsHtml += '<a href="{{ route("integrantes.archivos") }}?integrante_id=' + row.id + '" alt="Descargar archivos" title="Descargar archivos" target="_blank" style="margin-right: 5px;"><i class="fa fa-download"></i></a>';




                            }
                            if(row.estado == 'Alta Creada') {
                                @can('integrante-editar')
                                    actionsHtml += '<form id="send-form-' + row.id + '" method="post" action="{{ route('integrantes.enviar', '') }}/' + row.id + '" style="display: none">';
                                    actionsHtml += '{{ csrf_field() }}';

                                    actionsHtml += '</form>';
                                    actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'send-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar Alta" title="Enviar Alta" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                @endcan
                            }

                            if(row.estado == 'Baja Creada') {
                                @can('integrante-eliminar')
                                    actionsHtml += '<form id="sendbaja-form-' + row.id + '" method="post" action="{{ route('integrantes.enviarBaja', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'sendbaja-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar Baja" title="Enviar Baja" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                @endcan
                            }

                            if(row.estado == 'Cambio Creado') {
                                @can('integrante-editar')
                                    actionsHtml += '<form id="sendcambio-form-' + row.id + '" method="post" action="{{ route('integrantes.enviarCambio', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'sendcambio-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar Cambio de colaborador a integrante" title="Enviar Cambio de colaborador a integrante" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                @endcan
                            }

                            if(row.estado == 'Cambio Hs. Creado') {
                                @can('integrante-editar')
                                    actionsHtml += '<form id="sendcambioHS-form-' + row.id + '" method="post" action="{{ route('integrantes.enviarCambioHS', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'sendcambioHS-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar Cambio de dedicación horaria" title="Enviar Cambio de dedicación horaria" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                @endcan
                            }
                            if(row.estado == 'Cambio Tipo Creado') {
                                @can('integrante-editar')
                                    actionsHtml += '<form id="sendcambioTipo-form-' + row.id + '" method="post" action="{{ route('integrantes.enviarCambioTipo', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'sendcambioTipo-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar Cambio de tipo" title="Enviar Cambio de tipo" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                @endcan
                            }
                            if(row.estado == 'Alta Recibida') {
                                @can('solicitud-admitir')
                                    actionsHtml += '<form id="admit-form-' + row.id + '" method="post" action="{{ route('integrantes.admitir', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admit-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir alta" title="Admitir alta" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                @endcan
                                    @can('solicitud-rechazar')
                                    actionsHtml += '<a href="{{ route("integrantes.rechazar", ":id") }}" alt="Rechazar alta" title="Rechazar alta" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                                @endcan
                            }

                            if(row.estado == 'Baja Recibida') {
                                @can('solicitud-admitir')
                                    actionsHtml += '<form id="admitBaja-form-' + row.id + '" method="post" action="{{ route('integrantes.admitirBaja', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admitBaja-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir baja" title="Admitir baja" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                @endcan
                                    @can('solicitud-rechazar')
                                    actionsHtml += '<a href="{{ route("integrantes.rechazarBaja", ":id") }}" alt="Rechazar baja" title="Rechazar baja" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                                @endcan
                            }


                            if(row.estado == 'Cambio Recibido') {
                                @can('solicitud-admitir')
                                    actionsHtml += '<form id="admitCambio-form-' + row.id + '" method="post" action="{{ route('integrantes.admitirCambio', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admitCambio-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir cambio de colaborador a integrante" title="Admitir cambio de colaborador a integrante" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                @endcan
                                    @can('solicitud-rechazar')
                                    actionsHtml += '<a href="{{ route("integrantes.rechazarCambio", ":id") }}" alt="Rechazar cambio de colaborador a integrante" title="Rechazar cambio de colaborador a integrante" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                                @endcan
                            }

                            if(row.estado == 'Cambio Hs. Recibido') {
                                @can('solicitud-admitir')
                                    actionsHtml += '<form id="admitHS-form-' + row.id + '" method="post" action="{{ route('integrantes.admitirCambioHS', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admitHS-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir cambio de dedicación horaria" title="Admitir cambio de dedicación horaria" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                @endcan
                                    @can('solicitud-rechazar')
                                    actionsHtml += '<a href="{{ route("integrantes.rechazarCambioHS", ":id") }}" alt="Rechazar cambio de dedicación horaria" title="Rechazar cambio de dedicación horaria" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                                @endcan
                            }
                            if(row.estado == 'Cambio Tipo Recibido') {
                                @can('solicitud-admitir')
                                    actionsHtml += '<form id="admitTipo-form-' + row.id + '" method="post" action="{{ route('integrantes.admitirCambioTipo', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admitTipo-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir cambio de tipo" title="Admitir cambio de tipo" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                @endcan
                                    @can('solicitud-rechazar')
                                    actionsHtml += '<a href="{{ route("integrantes.rechazarCambioTipo", ":id") }}" alt="Rechazar cambio de tipo" title="Rechazar cambio de tipo" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
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
