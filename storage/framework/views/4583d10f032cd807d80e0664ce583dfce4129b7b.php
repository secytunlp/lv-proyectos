

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
                <i class="fa fa-cogs" aria-hidden="true"></i>Proyectos
                <!--<small>Create, Read, Update, Delete</small>-->
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('proyectos.index')); ?>">Proyectos</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Proyecto</h3>
                            <!--<a class='pull-right btn btn-success' href="<?php echo e(route('proyectos.create')); ?>">Nuevo</a>-->
                        </div>
                        <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
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
                                    <th>Acciones</th>
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
                    "url": "<?php echo e(route('proyectos.dataTable')); ?>",
                    "type": "POST",
                    "data": function (d) {
                        d._token = '<?php echo e(csrf_token()); ?>'; // Agrega el token CSRF si estás usando Laravel
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
                        "data": "id",
                        "orderable": false,
                        "searchable": false,
                        "render": function(data, type, row) {
                            // Construir HTML para las acciones
                            var actionsHtml = '';
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('proyecto-listar')): ?>
                                actionsHtml += '<a href="<?php echo e(route("proyectos.show", ":id")); ?>" alt="Ver proyecto" title="Ver proyecto" style="margin-right: 5px;"><span class="glyphicon glyphicon-eye-open"></span></a>'.replace(':id', row.id);
                            <?php endif; ?>
// Agregar enlace de edición si el usuario tiene permiso
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('integrante-listar')): ?>
                                actionsHtml += '<a href="<?php echo e(route("integrantes.index")); ?>?proyecto_id=' + row.id + '" alt="Integrantes" title="Integrantes"><i class="fa fa-user-friends"></i></a>';
                            <?php endif; ?>


                        return actionsHtml;

                    },
            }
                ],
                "language": {
                    "url": "<?php echo e(asset('bower_components/datatables.net/lang/es-AR.json')); ?>"
                },
                stateSave: true,
                "initComplete": function() {

                }
            });
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/proyectos/index.blade.php ENDPATH**/ ?>