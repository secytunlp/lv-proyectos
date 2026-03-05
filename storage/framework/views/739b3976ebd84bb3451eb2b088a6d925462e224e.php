

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
                <i class="fa fa-university" aria-hidden="true"></i>Universidades
                <!--<small>Create, Read, Update, Delete</small>-->
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('universidads.index')); ?>">Universidades</a></li>
                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Universidad</h3>
                            <a class='pull-right btn btn-success' href="<?php echo e(route('universidads.create')); ?>">Nuevo</a>
                        </div>
                        <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


                    <!-- /.box-header -->
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Nombre</th>

                                    <th>Acciones</th>
                                </tr>
                                </thead>
                                <tbody>
        <?php $__currentLoopData = $universidads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $universidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <!--<td><?php echo e($loop->index + 1); ?></td>-->
                <td><?php echo e($universidad->nombre); ?></td>

                <td><?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('universidad-editar')): ?><a href="<?php echo e(route('universidads.edit',$universidad->id)); ?>" alt="Editar" title="Editar"><span class="glyphicon glyphicon-edit"></span></a><?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('universidad-eliminar')): ?>
                    <form id="delete-form-<?php echo e($universidad->id); ?>" method="post" action="<?php echo e(route('universidads.destroy',$universidad->id)); ?>" style="display: none">
                        <?php echo e(csrf_field()); ?>

                        <?php echo e(method_field('DELETE')); ?>

                    </form>

                    <a href="" onclick="
                        if(confirm('Está seguro?'))
                        {
                        event.preventDefault();
                        document.getElementById('delete-form-<?php echo e($universidad->id); ?>').submit();
                        }
                        else{
                        event.preventDefault();
                        }" alt="Eliminar" title="Eliminar"><span class="glyphicon glyphicon-trash"></span></a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <!--<th>Nro.</th>-->
                                    <th>Nombre</th>

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
                "autoWidth": false, // Desactiva el ajuste automático del anchos
                responsive: true,
                scrollX: true,
                "language": {
                    "url": "<?php echo e(asset('bower_components/datatables.net/lang/es-AR.json')); ?>"
                }
            });
        });

    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/universidads/index.blade.php ENDPATH**/ ?>