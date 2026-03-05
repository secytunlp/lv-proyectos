<?php $__env->startSection('headSection'); ?>

    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/skins/_all-skins.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('bower_components/select2/dist/css/select2.min.css')); ?>">

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-graduation-cap" aria-hidden="true"></i>Título
                <small>Editar</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('titulos.index')); ?>">Títulos</a></li>
                <!--<li class="active">Edit Form</li>-->
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Editar</h3>
                        </div>
                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="<?php echo e(route('titulos.update',$titulo->id)); ?>" method="post">
                            <?php echo e(csrf_field()); ?>

                            <?php echo e(method_field('PUT')); ?>

                            <div class="box-body">

                                    <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-lg-6 col-md-3">
                                    <div class="form-group">
                                        <label for="nombre">Nombre</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" value="<?php if(old('nombre')): ?><?php echo e(old('nombre')); ?><?php else: ?><?php echo e($titulo->nombre); ?><?php endif; ?>">
                                    </div>
                                    </div>
                                </div>

                                <div class="col-lg-offset-3 col-lg-6 col-md-3">
                                    <div class="form-group">
                                        <label for="name">Nivel</label>
                                        <?php echo e(Form::select('nivel',['Grado'=>'Grado','Posgrado'=>'Posgrado'], $titulo->nivel,['class' => 'form-control','id'=>'nivel'])); ?>

                                    </div>
                                </div>
                                <div class="col-lg-offset-3 col-lg-6 col-md-4">
                                    <div class="form-group">
                                        <?php echo e(Form::label('universidad', 'Universidad')); ?>

                                        <?php echo e(Form::select('universidad_id', $universidads,($titulo->universidad)?$titulo->universidad->id:'', ['class' => 'form-control js-example-basic-single','id'=>'universidad_id'])); ?>

                                    </div>

                                </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href='<?php echo e(route('titulos.index')); ?>' class="btn btn-warning">Volver</a>
                                    </div>
                            </div>



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
<?php $__env->startSection('footerSection'); ?>
    <!-- jQuery 3 -->
    <script src="<?php echo e(asset('bower_components/jquery/dist/jquery.min.js')); ?>"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="<?php echo e(asset('bower_components/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
    <!-- SlimScroll -->
    <script src="<?php echo e(asset('bower_components/jquery-slimscroll/jquery.slimscroll.min.js')); ?>"></script>
    <!-- FastClick -->
    <script src="<?php echo e(asset('bower_components/fastclick/lib/fastclick.js')); ?>"></script>

    <!-- Select2 -->
    <script src="<?php echo e(asset('bower_components/select2/dist/js/select2.min.js')); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo e(asset('dist/js/adminlte.min.js')); ?>"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo e(asset('dist/js/demo.js')); ?>"></script>
    <!-- page script -->
    <script>
        $(document).ready(function () {

            $('.js-example-basic-single').select2();
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/titulos/edit.blade.php ENDPATH**/ ?>