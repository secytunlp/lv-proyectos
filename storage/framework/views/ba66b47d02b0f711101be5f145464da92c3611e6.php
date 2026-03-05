

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
                <i class="fa fa-user-friends" aria-hidden="true"></i> Integrantes

            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('integrantes.index')); ?>">Integrantes</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Integrantes <?php if($proyecto): ?> - <?php echo e($proyecto->codigo); ?><?php endif; ?></h3>
                            <?php if($proyecto): ?><a class='pull-right btn btn-success' href="<?php echo e(route('integrantes.create', ['proyecto_id' => $proyecto->id])); ?>">Nuevo</a><?php endif; ?>
                        </div>
                        <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


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
                "order": [[ 2, "asc" ]], // Ordenar por el tercer campo (apellido) de forma ascendente
                "ajax": {
                    "url": "<?php echo e(route('integrantes.dataTable')); ?>",
                    "type": "POST",
                    "data": function (d) {
                        d._token = '<?php echo e(csrf_token()); ?>'; // Agrega el token CSRF si estás usando Laravel
                        d.proyecto_id = '<?php echo e($proyecto ? $proyecto->id : ''); ?>'; // Enviar el ID del proyecto como filtro
                        // Agrega otros parámetros si es necesario
                        // d.otroParametro = valor;
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
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-listar')): ?>
                                actionsHtml += '<a href="<?php echo e(route("integrantes.show", ":id")); ?>" alt="Ver integrante" title="Ver integrante" style="margin-right: 5px;"><span class="glyphicon glyphicon-eye-open"></span></a>'.replace(':id', row.id);
                            <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante_estado-listar')): ?>
                                actionsHtml += '<a href="<?php echo e(route("integrante_estados.index")); ?>?integrante_id=' + row.id + '" alt="Estados" title="Estados" style="margin-right: 5px;"><i class="fa fa-tasks"></i></a>';
                            <?php endif; ?>




                            //console.log(row.estado);
                            if(row.estado == 'Alta Creada') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-editar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.edit", ":id")); ?>" alt="Modificar alta" title="Modificar alta" style="margin-right: 5px;"><span class="glyphicon glyphicon-edit"></span></a>'.replace(':id', row.id);
                                <?php endif; ?>
                                // Agregar formulario de eliminación si el usuario tiene permiso
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-eliminar')): ?>
                                    actionsHtml += '<form id="delete-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.destroy', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';
                                actionsHtml += '<?php echo e(method_field('DELETE')); ?>';
                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'delete-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Anular alta" title="Anular alta" style="margin-right: 5px;"><span class="glyphicon glyphicon-trash"></span></a>';
                                <?php endif; ?>
                            }

                            if(((row.estado == '')||(row.estado == null)) &&(row.baja == null)&&(row.tipo != 'Director')&&(row.tipo != 'Codirector')){
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-eliminar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.baja", ":id")); ?>" alt="Baja de integrante" title="Baja de integrante" style="margin-right: 5px;"><span class="glyphicon glyphicon-remove"></span></a>'.replace(':id', row.id);
                                <?php endif; ?>

                            }
                            if(row.estado == 'Baja Creada') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-eliminar')): ?>
                                    actionsHtml += '<form id="anular-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.anular', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'anular-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Anular Baja" title="Anular Baja" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>';
                                <?php endif; ?>

                            }
                            if(((row.estado == '')||(row.estado == null)) &&(row.baja == null) &&(row.tipo == 'Colaborador')){
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-editar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.cambio", ":id")); ?>" alt="Cambio de colaborador a integrante" title="Cambio de colaborador a integrante" style="margin-right: 5px;"><span class="glyphicon glyphicon-refresh"></span></a>'.replace(':id', row.id);
                                <?php endif; ?>

                            }
                            if(((row.estado == '')||(row.estado == null)) &&(row.baja == null) &&(row.tipo != 'Colaborador')){
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-editar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.cambioHS", ":id")); ?>" alt="Cambio de dedicación horaria" title="Cambio de dedicación horaria" style="margin-right: 5px;"><i class="fa fa-clock"></i></a>'.replace(':id', row.id);
                                <?php endif; ?>

                            }
                            if(((row.estado == '')||(row.estado == null)) &&(row.baja == null)&&(row.tipo != 'Director')&&(row.tipo != 'Codirector')&&(row.tipo != 'Colaborador')){
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-editar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.cambioTipo", ":id")); ?>" alt="Cambio de tipo de integrante" title="Cambio de tipo de integrante" style="margin-right: 5px;"><span class="glyphicon glyphicon-refresh"></span></a>'.replace(':id', row.id);
                                <?php endif; ?>

                            }
                            if(row.estado == 'Cambio Creado') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-editar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.cambio", ":id")); ?>" alt="Cambio de colaborador a integrante" title="Cambio de colaborador a integrante" style="margin-right: 5px;"><span class="glyphicon glyphicon-refresh"></span></a>'.replace(':id', row.id);
                                actionsHtml += '<form id="anularCambio-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.anularCambio', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'anularCambio-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Anular Cambio de colaborador a integrante" title="Anular Cambio de colaborador a integrante" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>';
                                <?php endif; ?>

                            }
                            if(row.estado == 'Cambio Hs. Creado') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-editar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.cambioHS", ":id")); ?>" alt="Cambio de dedicación horaria" title="Cambio de dedicación horaria" style="margin-right: 5px;"><span class="fa fa-clock"></span></a>'.replace(':id', row.id);
                                    actionsHtml += '<form id="anularHS-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.anularHS', '')); ?>/' + row.id + '" style="display: none">';
                                    actionsHtml += '<?php echo e(csrf_field()); ?>';

                                    actionsHtml += '</form>';
                                    actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'anularHS-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Anular Cambio de dedicación horaria" title="Anular Cambio de dedicación horaria" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>';
                                <?php endif; ?>

                            }
                            if(row.estado == 'Cambio Tipo Creado') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-editar')): ?>

                                actionsHtml += '<form id="anularTipo-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.anularTipo', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'anularTipo-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Anular Cambio de tipo" title="Anular Cambio de tipo" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>';
                                <?php endif; ?>

                            }

                            if((row.estado == 'Alta Recibida')||(row.estado == 'Alta Creada')) {

                                // Agregar enlace para descargar el PDF del integrante
                                actionsHtml += '<a href="<?php echo e(route("integrantes.alta-pdf")); ?>?integrante_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';

                                actionsHtml += '<a href="<?php echo e(route("integrantes.archivos")); ?>?integrante_id=' + row.id + '" alt="Descargar archivos" title="Descargar archivos" target="_blank" style="margin-right: 5px;"><i class="fa fa-download"></i></a>';




                            }
                            if((row.estado == 'Baja Recibida')||(row.estado == 'Baja Creada')) {

                                // Agregar enlace para descargar el PDF del integrante
                                actionsHtml += '<a href="<?php echo e(route("integrantes.baja-pdf")); ?>?integrante_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';






                            }
                            if((row.estado == 'Cambio Recibido')||(row.estado == 'Cambio Creado')) {

                                // Agregar enlace para descargar el PDF del integrante
                                actionsHtml += '<a href="<?php echo e(route("integrantes.cambio-pdf")); ?>?integrante_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';

                                actionsHtml += '<a href="<?php echo e(route("integrantes.archivos")); ?>?integrante_id=' + row.id + '" alt="Descargar archivos" title="Descargar archivos" target="_blank" style="margin-right: 5px;"><i class="fa fa-download"></i></a>';




                            }
                            if((row.estado == 'Cambio Hs. Recibido')||(row.estado == 'Cambio Hs. Creado')) {

                                // Agregar enlace para descargar el PDF del integrante
                                actionsHtml += '<a href="<?php echo e(route("integrantes.cambioHS-pdf")); ?>?integrante_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';


                                actionsHtml += '<a href="<?php echo e(route("integrantes.archivos")); ?>?integrante_id=' + row.id + '" alt="Descargar archivos" title="Descargar archivos" target="_blank" style="margin-right: 5px;"><i class="fa fa-download"></i></a>';



                            }
                            if((row.estado == 'Cambio Tipo Recibido')||(row.estado == 'Cambio Tipo Creado')) {

                                // Agregar enlace para descargar el PDF del integrante
                                actionsHtml += '<a href="<?php echo e(route("integrantes.cambioTipo-pdf")); ?>?integrante_id=' + row.id + '" alt="Descargar PDF" title="Descargar PDF" target="_blank" style="margin-right: 5px;"><i class="fa fa-file-pdf"></i></a>';

                                actionsHtml += '<a href="<?php echo e(route("integrantes.archivos")); ?>?integrante_id=' + row.id + '" alt="Descargar archivos" title="Descargar archivos" target="_blank" style="margin-right: 5px;"><i class="fa fa-download"></i></a>';




                            }
                            if(row.estado == 'Alta Creada') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-editar')): ?>
                                    actionsHtml += '<form id="send-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.enviar', '')); ?>/' + row.id + '" style="display: none">';
                                    actionsHtml += '<?php echo e(csrf_field()); ?>';

                                    actionsHtml += '</form>';
                                    actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'send-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar Alta" title="Enviar Alta" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                <?php endif; ?>
                            }

                            if(row.estado == 'Baja Creada') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-eliminar')): ?>
                                    actionsHtml += '<form id="sendbaja-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.enviarBaja', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'sendbaja-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar Baja" title="Enviar Baja" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                <?php endif; ?>
                            }

                            if(row.estado == 'Cambio Creado') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-editar')): ?>
                                    actionsHtml += '<form id="sendcambio-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.enviarCambio', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'sendcambio-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar Cambio de colaborador a integrante" title="Enviar Cambio de colaborador a integrante" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                <?php endif; ?>
                            }

                            if(row.estado == 'Cambio Hs. Creado') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-editar')): ?>
                                    actionsHtml += '<form id="sendcambioHS-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.enviarCambioHS', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'sendcambioHS-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar Cambio de dedicación horaria" title="Enviar Cambio de dedicación horaria" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                <?php endif; ?>
                            }
                            if(row.estado == 'Cambio Tipo Creado') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-editar')): ?>
                                    actionsHtml += '<form id="sendcambioTipo-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.enviarCambioTipo', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Luego de enviar la solicitud no podrá realizar modificaciones ¿Continua?\')) {event.preventDefault(); document.getElementById(\'sendcambioTipo-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Enviar Cambio de tipo" title="Enviar Cambio de tipo" style="margin-right: 5px;"><i class="fa fa-paper-plane"></i></a>';
                                <?php endif; ?>
                            }
                            if(row.estado == 'Alta Recibida') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-admitir')): ?>
                                    actionsHtml += '<form id="admit-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.admitir', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admit-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir alta" title="Admitir alta" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-rechazar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.rechazar", ":id")); ?>" alt="Rechazar alta" title="Rechazar alta" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                                <?php endif; ?>
                            }

                            if(row.estado == 'Baja Recibida') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-admitir')): ?>
                                    actionsHtml += '<form id="admitBaja-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.admitirBaja', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admitBaja-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir baja" title="Admitir baja" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-rechazar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.rechazarBaja", ":id")); ?>" alt="Rechazar baja" title="Rechazar baja" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                                <?php endif; ?>
                            }


                            if(row.estado == 'Cambio Recibido') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-admitir')): ?>
                                    actionsHtml += '<form id="admitCambio-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.admitirCambio', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admitCambio-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir cambio de colaborador a integrante" title="Admitir cambio de colaborador a integrante" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-rechazar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.rechazarCambio", ":id")); ?>" alt="Rechazar cambio de colaborador a integrante" title="Rechazar cambio de colaborador a integrante" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                                <?php endif; ?>
                            }

                            if(row.estado == 'Cambio Hs. Recibido') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-admitir')): ?>
                                    actionsHtml += '<form id="admitHS-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.admitirCambioHS', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admitHS-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir cambio de dedicación horaria" title="Admitir cambio de dedicación horaria" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-rechazar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.rechazarCambioHS", ":id")); ?>" alt="Rechazar cambio de dedicación horaria" title="Rechazar cambio de dedicación horaria" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                                <?php endif; ?>
                            }
                            if(row.estado == 'Cambio Tipo Recibido') {
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-admitir')): ?>
                                    actionsHtml += '<form id="admitTipo-form-' + row.id + '" method="post" action="<?php echo e(route('integrantes.admitirCambioTipo', '')); ?>/' + row.id + '" style="display: none">';
                                actionsHtml += '<?php echo e(csrf_field()); ?>';

                                actionsHtml += '</form>';
                                actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'admitTipo-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Admitir cambio de tipo" title="Admitir cambio de tipo" style="margin-right: 5px;"><i class="fa fa-check-circle"></i></a>';
                                <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('solicitud-rechazar')): ?>
                                    actionsHtml += '<a href="<?php echo e(route("integrantes.rechazarCambioTipo", ":id")); ?>" alt="Rechazar cambio de tipo" title="Rechazar cambio de tipo" style="margin-right: 5px;"><i class="fa fa-times-circle"></i></a>'.replace(':id', row.id);
                                <?php endif; ?>
                            }


                            return actionsHtml;

                    },
            }
                ],
                "language": {
                    "url": "<?php echo e(asset('bower_components/datatables.net/lang/es-AR.json')); ?>"
                },
                "initComplete": function() {
                    // Recuperar el valor del filtro desde la sesión
                    var filtroGuardado = '<?php echo e(session('nombre_filtro_integrante', '')); ?>';

                    // Establecer el valor en el input de búsqueda
                    if (filtroGuardado) {
                        $('#example1_filter input[type="search"]').val(filtroGuardado);
                    }

                    // Agregar botón "Limpiar Filtro" justo después del input de búsqueda
                    $('#example1_filter').append('<button id="clearFilter" class="btn btn-secondary btn-sm" style="margin-left: 10px;">Limpiar Filtro</button>');

                    // Asignar acción al botón "Limpiar Filtro"
                    $('#clearFilter').click(function() {
                        // Enviar una solicitud al servidor para limpiar la sesión
                        $.post("<?php echo e(route('integrantes.clearFilter')); ?>", {
                            _token: '<?php echo e(csrf_token()); ?>'
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
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/integrantes/index.blade.php ENDPATH**/ ?>