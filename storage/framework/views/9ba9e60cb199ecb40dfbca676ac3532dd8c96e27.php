

<?php $__env->startSection('headSection'); ?>
    <!-- DataTables -->
    <link rel="stylesheet" href="<?php echo e(asset('bower_components/datatables.net-bs/css/dataTables.bootstrap.css')); ?>">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/skins/_all-skins.min.css')); ?>">

<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-layer-group" aria-hidden="true"></i>Solicitudes SICADI
                <!--<small>Create, Read, Update, Delete</small>-->
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('solicitud_sicadis.index')); ?>">Solicitudes</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-rechazar')): ?>
                                <?php if(session('selected_rol') == 1): ?>
                                    <a class='pull-right btn btn-info' href="<?php echo e(route('solicitud_sicadis.importar')); ?>" style="margin-left: 5px">Importar</a>
                                <?php endif; ?>
                            <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-rechazar')): ?>
                                    <a class='pull-right btn btn-info' style="margin-left: 5px;" id="exportar-datos" href="#">Exportar</a>
                                <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud_sicadi-crear')): ?>
                                <a class='pull-right btn btn-success' href="<?php echo e(route('solicitud_sicadis.create')); ?>">Nueva</a>
                            <?php endif; ?>
                        </div>
                        <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                        <?php
                            $currentYear = date('Y');
                        ?>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="filtroYear">Año:</label>
                                        <select id="filtroYear" class="form-control" style="width: 100%">
                                            <option value="-1">Todos</option>
                                            <?php for($year = 2024; $year <= $currentYear; $year++): ?>
                                                <option value="<?php echo e($year); ?>"
                                                    <?php echo e($year == $currentAnio ? 'selected' : ''); ?>>
                                                    <?php echo e($year); ?>

                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <?php echo e(Form::label('tipo', 'Convocatoria')); ?>

                                        <?php echo e(Form::select('tipo',['-1'=>'Todas']+ config('convocatorias'), '',['class' => 'form-control'])); ?>


                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <?php echo e(Form::label('mecanismo', 'Mecanismo')); ?>

                                        <?php echo e(Form::select('mecanismo',['-1'=>'Todos']+ config('mecanismos'), '',['class' => 'form-control'])); ?>


                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <?php echo e(Form::label('solicitada', 'Solicitada')); ?>

                                        <?php echo e(Form::select('solicitada',['-1'=>'Todas']+ config('categorias'), '',['class' => 'form-control'])); ?>


                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <?php echo e(Form::label('estado', 'Estado')); ?>

                                        <?php echo e(Form::select('estado',['-1'=>'Todos']+ config('estados'), '',['class' => 'form-control'])); ?>


                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group">
                                        <?php echo e(Form::label('presentacion_ua', 'UA')); ?>

                                        <?php echo e(Form::select('presentacion_ua',['-1'=>'Todas']+ config('facultades'), '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%'])); ?>




                                    </div>
                                </div>

                                <div class="col-md-1">
                                    <div class="form-group">
                                        <?php echo e(Form::label('asignada', 'Asignada')); ?>

                                        <?php echo e(Form::select('asignada',['-1'=>'Todas']+ config('categorias'), '',['class' => 'form-control'])); ?>


                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <?php echo e(Form::label('otorgadas', 'Otorgadas')); ?><br>
                                        <?php echo e(Form::checkbox('otorgadas', 1,false)); ?>



                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'components.select-areas','data' => ['areas' => $areas,'subareas' => $subareas]]); ?>
<?php $component->withName('select-areas'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['areas' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($areas),'subareas' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($subareas)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
                                </div>
                            </div>
                            <!-- /.form-group -->
                        </div>                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped table-hover">
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
                                    <th>Área</th>
                                    <th>Subárea</th>
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
                                    <th>Área</th>
                                    <th>Subárea</th>

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
<?php $__env->stopSection(); ?>
<?php $__env->startSection('footerSection'); ?>
    <!-- jQuery 3 -->
    <script src="<?php echo e(asset('bower_components/jquery/dist/jquery.min.js')); ?>"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo e(asset('bower_components/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
    <!-- DataTables -->
    <script src="<?php echo e(asset('bower_components/datatables.net/js/jquery.dataTables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js')); ?>"></script>
    <!-- SlimScroll -->
    <script src="<?php echo e(asset('bower_components/jquery-slimscroll/jquery.slimscroll.min.js')); ?>"></script>
    <!-- FastClick -->
    <script src="<?php echo e(asset('bower_components/fastclick/lib/fastclick.js')); ?>"></script>
    <!-- FastClick -->
    <script src="<?php echo e(asset('bower_components/fastclick/lib/fastclick.js')); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo e(asset('dist/js/adminlte.min.js')); ?>"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo e(asset('dist/js/demo.js')); ?>"></script>
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
                    "url": "<?php echo e(route('solicitud_sicadis.dataTable')); ?>",
                    "type": "POST",
                    "data": function (d) {
                        d._token = '<?php echo e(csrf_token()); ?>'; // Agrega el token CSRF si estás usando Laravel
                        d.filtroYear = $('#filtroYear').val(); // Agregar el valor del filtro de período
                        d.tipo = $('#tipo').val();
                        d.mecanismo = $('#mecanismo').val();
                        d.solicitada = $('#solicitada').val();

                        d.estado = $('#estado').val();


                        d.presentacion_ua = $('#presentacion_ua').val();
                        d.asignada = $('#asignada').val();
                        d.otorgadas = $('#otorgadas').is(':checked') ? 1 : 0;
                        d.area = $('#area').val();
                        d.subarea = $('#subarea').val();
                        // Agrega otros parámetros si es necesario
                        // d.otroParametro = valor;
                    },
                    "error": function(xhr, error, thrown) {
                        if (xhr.status === 401) {
                            // Usuario no autenticado, redirigir al login
                            window.location.href = "<?php echo e(route('login')); ?>";
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
                    {data: 'area', name: 'area'},
                    {data: 'subarea', name: 'subarea'},



                    {
                        "data": "id",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, row) {
                            // Construir HTML para las acciones
                            var actionsHtml = '';
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud_sicadi_estado-listar')): ?>
                                actionsHtml += '<a href="<?php echo e(route("solicitud_sicadi_estados.index")); ?>?solicitud_sicadi_id=' + row.id + '" alt="Estados" title="Estados" style="margin-right: 5px;"><i class="fa fa-tasks"></i></a>';
                            <?php endif; ?>
                                <?php if(session('selected_rol') == 2): ?>
                                if (row.estado == 'Creada') {
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud_sicadi-editar')): ?>
                                        actionsHtml += '<a href="<?php echo e(route("solicitud_sicadis.edit", ":id")); ?>" alt="Editar" title="Editar"><span class="glyphicon glyphicon-edit"></span></a>'.replace(':id', row.id);
                                    <?php endif; ?>
                                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud_sicadi-eliminar')): ?>
                                        actionsHtml += '<form id="delete-form-' + row.id + '" method="post" action="<?php echo e(route('solicitud_sicadis.destroy', '')); ?>/' + row.id + '" style="display: none">';
                                        actionsHtml += '<?php echo e(csrf_field()); ?>';
                                        actionsHtml += '<?php echo e(method_field('DELETE')); ?>';
                                        actionsHtml += '</form>';
                                        actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'delete-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Eliminar" title="Eliminar" style="margin-right: 5px;"><span class="glyphicon glyphicon-trash"></span></a>';
                                    <?php endif; ?>
                                }
                            <?php else: ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud_sicadi-editar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("solicitud_sicadis.edit", ":id")); ?>" alt="Editar" title="Editar"><span class="glyphicon glyphicon-edit"></span></a>'.replace(':id', row.id);
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud_sicadi-eliminar')): ?>
                                actionsHtml += '<form id="delete-form-' + row.id + '" method="post" action="<?php echo e(route('solicitud_sicadis.destroy', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';
                                actionsHtml += '<?php echo e(method_field('DELETE')); ?>';
                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'delete-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Eliminar" title="Eliminar" style="margin-right: 5px;"><span class="glyphicon glyphicon-trash"></span></a>';
                                <?php endif; ?>
                            <?php endif; ?>


                            // Agregar enlace para descargar el PDF del integrante
                            actionsHtml += '<a href="<?php echo e(route("solicitud_sicadis.solicitud-pdf")); ?>?solicitud_sicadi_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';

                            actionsHtml += '<a href="<?php echo e(route("solicitud_sicadis.archivos")); ?>?solicitud_sicadi_id=' + row.id + '" alt="Descargar archivos" title="Descargar archivos" target="_blank" style="margin-right: 5px;"><i class="fa fa-download"></i></a>';


                            if (row.estado == 'Creada') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-editar')): ?>

                                    actionsHtml += '<form id="send-form-' + row.id + '" method="post" action="<?php echo e(route('solicitud_sicadis.enviar', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'send-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar" title="Enviar" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                <?php endif; ?>
                            }
                            if (row.estado == 'Recibida') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-admitir')): ?>
                                    actionsHtml += '<form id="admit-form-' + row.id + '" method="post" action="<?php echo e(route('solicitud_sicadis.admitir', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admit-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir" title="Admitir" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-rechazar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("solicitud_sicadis.rectificar", ":id")); ?>" alt="Rectificar" title="Rectificar" style="margin-right: 5px;"><i class="fa fa-pencil-alt"></i></a>'.replace(':id', row.id);
                                <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-rechazar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("solicitud_sicadis.rechazar", ":id")); ?>" alt="Rechazar" title="Rechazar" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                                <?php endif; ?>
                            }
                        return actionsHtml;

                    },
                }
                ],
                "language": {
                    "url": "<?php echo e(asset('bower_components/datatables.net/lang/es-AR.json')); ?>"
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
                    data.area = $('#area').val();
                    data.subarea = $('#subarea').val();
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
                    if (data.area) {
                        $('#area').val(data.area).trigger('change');
                    }
                    if (data.subarea) {
                        $('#subarea').val(data.subarea).trigger('change');
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

            $('#area').change(function() {
                table.ajax.reload(null, false);
            });

            $('#subarea').change(function() {
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
                    area: $('#area').val(),
                    subarea: $('#subarea').val(),
                    // Captura el valor del filtro nativo del DataTable
                    busqueda: $('input[type="search"]').val() // Ajusta el selector según tu configuración


                };

                // Realiza una solicitud GET para exportar los datos
                window.location.href = '<?php echo e(route('solicitud_sicadis.exportar')); ?>?' + $.param(filtros);
            });

        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/solicitud_sicadis/index.blade.php ENDPATH**/ ?>