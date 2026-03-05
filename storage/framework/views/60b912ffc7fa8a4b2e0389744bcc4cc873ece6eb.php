

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
                <i class="fa fa-microscope" aria-hidden="true"></i> Estados

            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('jovens.index')); ?>">Jóvenes Investigadores</a></li>
                <li><a href="<?php echo e(route('joven_estados.index')); ?>">Estados</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Estados <?php if($joven): ?> - <?php echo e($joven->investigador->persona->apellido); ?> <?php echo e($joven->investigador->persona->nombre); ?> en la solicitud <?php echo e($joven->periodo->nombre); ?><?php endif; ?></h3>
                            <?php if($joven): ?><a class='pull-right btn btn-success' href="<?php echo e(route('joven_estados.create', ['joven_id' => $joven->id])); ?>">Cambiar</a><?php endif; ?>
                        </div>
                        <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Solicitante</th>
                                    <th>Estado</th>
                                    <th>Solicitante</th>
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
                                    <th>Solicitante</th>
                                    <th>Estado</th>
                                    <th>Solicitante</th>
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
                "order": [[ 3, "desc" ]], // Ordenar por el tercer campo (apellido) de forma ascendente
                "ajax": {
                    "url": "<?php echo e(route('joven_estados.dataTable')); ?>",
                    "type": "POST",
                    "data": function (d) {
                        d._token = '<?php echo e(csrf_token()); ?>'; // Agrega el token CSRF si estás usando Laravel
                        d.joven_id = '<?php echo e($joven ? $joven->id : ''); ?>'; // Enviar el ID del joven como filtro
                        // Agrega otros parámetros si es necesario
                        // d.otroParametro = valor;
                    }
                },
                columns: [

                    {data: 'persona_nombre', name: 'persona_nombre', visible:false},
                    {data: 'estado', name: 'estado'},
                    {data: 'persona_apellido', name: 'persona_apellido'},

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
                        data: 'usuario_nombre', // Acceder al nombre de la usuario a través de la relación
                        name: 'usuario_nombre',
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
                    "url": "<?php echo e(asset('bower_components/datatables.net/lang/es-AR.json')); ?>"
                }
            });
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/joven_estados/index.blade.php ENDPATH**/ ?>