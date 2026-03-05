<!-- resources/views/welcome.blade.php -->


<?php $__env->startSection('content'); ?>
    <div class="content-wrapper">
        <section class="content-header">

        </section>
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div style="text-align: center">
                        <img src="<?php echo e(url('/images/secyt_unlp.PNG')); ?>" >
                    </div>
                        <div class="box-body">

                        <div class="login-logo">
                            <b>Plataforma de carga de datos</b><br>SECyT UNLP

                        </div>

                    </div>
                </div>
            </div>

        </section>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footerSection'); ?>
    <!-- jQuery 3 -->
    <script src="<?php echo e(asset('bower_components/jquery/dist/jquery.min.js')); ?>"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo e(asset('bower_components/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
    <script src="<?php echo e(asset('dist/js/adminlte.min.js')); ?>"></script>
    <!-- AdminLTE for demo purposes -->


<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/welcome.blade.php ENDPATH**/ ?>