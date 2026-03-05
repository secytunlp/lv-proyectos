

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
                <i class="fa fa-graduation-cap" aria-hidden="true"></i>Titulos
                <!--<small>Create, Read, Update, Delete</small>-->
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('titulos.index')); ?>">Titulos</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Titulo</h3>
                            <a class='pull-right btn btn-success' href="<?php echo e(route('titulos.create')); ?>">Nuevo</a>
                        </div>
                        <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Nombre</th>
                                    <th>Nivel</th>
                                    <th>Universidad</th>
                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Nombre</th>
                                    <th>Nivel</th>
                                    <th>Universidad</th>
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
            $('#example1').DataTable({
                "processing": true, // Activar la indicación de procesamiento
                "serverSide": true, // Habilitar el procesamiento del lado del servidor
                "autoWidth": false, // Desactiva el ajuste automático del anchos
                responsive: true,
                scrollX: true,
                paging : true,
                "ajax": {
                    "url": "<?php echo e(route('titulos.dataTable')); ?>",
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

                    {data: 'titulo_nombre', name: 'titulo_nombre'},
                    {data: 'nivel', name: 'nivel'},

                    {
                        data: 'universidad_nombre', // Acceder al nombre de la universidad a través de la relación
                        name: 'universidad_nombre',
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

                            // Agregar enlace de edición si el usuario tiene permiso
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('titulo-editar')): ?>
                                actionsHtml += '<a href="<?php echo e(route("titulos.edit", ":id")); ?>" alt="Editar" title="Editar"><span class="glyphicon glyphicon-edit"></span></a>'.replace(':id', row.id);
                            <?php endif; ?>

                            // Agregar formulario de eliminación si el usuario tiene permiso
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('titulo-eliminar')): ?>
                                actionsHtml += '<form id="delete-form-' + row.id + '" method="post" action="<?php echo e(route('titulos.destroy', '')); ?>/' + row.id + '" style="display: none">';
                            actionsHtml += '<?php echo e(csrf_field()); ?>';
                            actionsHtml += '<?php echo e(method_field('DELETE')); ?>';
                            actionsHtml += '</form>';
                            actionsHtml += '<a href="" onclick="if(confirm(\'Está seguro?\')) {event.preventDefault(); document.getElementById(\'delete-form-' + row.id + '\').submit();} else {event.preventDefault();}" alt="Eliminar" title="Eliminar"><span class="glyphicon glyphicon-trash"></span></a>';
                            <?php endif; ?>

                        return actionsHtml;

                    },
            }
                ],
                "language": {
                    "url": "<?php echo e(asset('bower_components/datatables.net/lang/es-AR.json')); ?>"
                },
                stateSave: true,
            });
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/titulos/index.blade.php ENDPATH**/ ?>