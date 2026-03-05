<?php $__env->startSection('content'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>

            </h1>
            <ol class="breadcrumb">

                <!--<li class="active">Data tables</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">

                        </div>
                        <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        <h2>403 - Usuario no tiene los permisos necesarios</h2>
                        <p>Lo sentimos, no tienes permisos para acceder a esta página.</p>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <!-- Panel para desloguearse -->
                        <form action="<?php echo e(route('logout')); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn btn-danger">Salir</button>
                        </form>
                    </div>
                    <!-- /.box -->


                </div>
                <!-- /.col-->
            </div>
            <!-- ./row -->
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.guest', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/errors/403.blade.php ENDPATH**/ ?>