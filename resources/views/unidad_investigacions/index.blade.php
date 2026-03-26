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
                <i class="fa fa-flask" aria-hidden="true"></i>Unidades De Investigación
                <!--<small>Create, Read, Update, Delete</small>-->
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('unidad_investigacions.index') }}">Unidades De Investigación</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">

                            <a class='pull-right btn btn-success' href="{{ route('unidad_investigacions.create') }}">Nueva</a>
                        </div>
                        @include('includes.messages')
                        <!-- Filtro de Período -->
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        {{Form::label('tipo', 'Tipo')}}
                                        {{ Form::select('tipo',['-1'=>'Todos']+config('unidadTipos'), '',['class' => 'form-control']) }}

                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        {{Form::label('estado', 'Estado')}}
                                        {{ Form::select('estado',['-1'=>'Todos','Creada'=>'Creada','Recibida'=>'Recibida','Admitida'=>'Admitida','No Admitida'=>'No Admitida','Rectificada'=>'Rectificada','Aprobada'=>'Aprobada'], '',['class' => 'form-control']) }}

                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        {{Form::label('facultad', 'U. Académica')}}
                                        {{Form::select('facultad_id',  $facultades,'', ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'facultad_id'])}}

                                    </div>
                                </div>
                            </div>

                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped table-hover">
                                <thead>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Tipo</th>
                                    <th>Denominación</th>
                                    <th>Sigla</th>
                                    <th>Especialidad</th>
                                    <th>Dependencias Académicas</th>
                                    <th>Fecha disposición</th>
                                    <th>Nro. disposición</th>


                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Tipo</th>
                                    <th>Denominación</th>
                                    <th>Sigla</th>
                                    <th>Especialidad</th>
                                    <th>Dependencias Académicas</th>
                                    <th>Fecha disposición</th>
                                    <th>Nro. disposición</th>


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
                    "url": "{{ route('unidad_investigacions.dataTable') }}",
                    "type": "POST",
                    "data": function (d) {
                        d._token = '{{ csrf_token() }}'; // Agrega el token CSRF si estás usando Laravel
                        d.tipo = $('#tipo').val(); // Agregar el valor del filtro de período
                        d.estado = $('#estado').val();

                        d.facultad = $('#facultad_id').val();
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

                    {data: 'tipo', name: 'tipo'},
                    {data: 'denominacion', name: 'denominacion'},
                    {data: 'sigla', name: 'sigla'},
                    {data: 'especialidad', name: 'especialidad'},
                    {data: 'facultads', name: 'facultads'},

                    {
                        data: 'fecha_disposicion',
                        name: 'fecha_disposicion',
                        render: function(data) {
                            // Verificar si la fecha de baja es null
                            if (data === null || data === '0000-00-00') {
                                return ''; // Devolver una cadena vacía si es null o un valor predeterminado no válido
                            }
                            // Formatear la fecha de fecha_disposicion en Y-m-d (sin hora)
                            return moment(data).format('DD/MM/YYYY');
                        }
                    },



                    {
                        data: 'disposicion', // Acceder al nombre de la facultad a través de la relación
                        name: 'disposicion',
                        orderable: true,
                        searchable: true
                    },
                    {data: 'estado', name: 'estado'},
                    {
                        "data": "id",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, row) {
                            // Construir HTML para las acciones
                            var actionsHtml = '';
                            @can('unidad-listar')
                                actionsHtml += '<a href="{{ route("unidad_investigacions.show", ":id") }}" alt="Ver unidad" title="Ver unidad" style="margin-right: 5px;"><span class="glyphicon glyphicon-eye-open"></span></a>'.replace(':id', row.id);
                            @endcan
                                @can('unidad_estado-listar')
                                actionsHtml += '<a href="{{ route("unidad_estados.index") }}?unidad_id=' + row.id + '" alt="Estados" title="Estados" style="margin-right: 5px;"><i class="fa fa-tasks"></i></a>';
                            @endcan
                            if (row.estado == 'Creada') {
                                @can('unidad-editar')
                                    actionsHtml += '<a href="{{ route("unidad_investigacions.edit", ":id") }}" alt="Modificar unidad" title="Modificar unidad" style="margin-right: 5px;"><span class="glyphicon glyphicon-edit"></span></a>'.replace(':id', row.id);
                                @endcan
                                // Agregar formulario de eliminación si el usuario tiene permiso
                                @can('unidad-eliminar')
                                    actionsHtml += '<form id="delete-form-' + row.id + '" method="post" action="{{ route('unidad_investigacions.destroy', '') }}/' + row.id + '" style="display: none">';
                                actionsHtml += '{{ csrf_field() }}';
                                actionsHtml += '{{ method_field('DELETE') }}';
                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'delete-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Eliminar" title="Eliminar" style="margin-right: 5px;"><span class="glyphicon glyphicon-trash"></span></a>';
                                @endcan
                            }
// Agregar enlace de edición si el usuario tiene permiso
                            @can('miembro-listar')
                                actionsHtml += '<a href="{{ route("miembros.index") }}?unidad_id=' + row.id + '" alt="Miembros" title="Miembros"><i class="fa fa-user-friends"></i></a>';
                            @endcan


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
                    data.tipo = $('#tipo').val();
                    data.estado = $('#estado').val();
                    data.area = $('#area').val();
                    data.facultad_id = $('#facultad_id').val();
                },
                stateLoadParams: function (settings, data) {
                    if (data.tipo) {
                        $('#tipo').val(data.tipo).trigger('change');
                    }
                    if (data.estado) {
                        $('#estado').val(data.estado).trigger('change');
                    }

                    if (data.facultad_id) {
                        $('#facultad_id').val(data.facultad_id).trigger('change');
                    }
                },
                "initComplete": function() {

                }
            });
            // Evento para manejar el cambio en el filtro de período
            $('#tipo').change(function() {
                table.ajax.reload(); // Recargar la tabla cuando cambie el filtro de período
            });
            $('#estado').change(function() {
                table.ajax.reload(); // Recargar la tabla cuando cambie el filtro de período
            });

            $('#facultad_id').change(function() {
                table.ajax.reload(); // Recargar la tabla cuando cambie el filtro de período
            });


        });

    </script>
@endsection
