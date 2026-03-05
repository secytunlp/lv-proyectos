
<?php $__env->startSection('headSection'); ?>

    <!-- AdminLTE Skins. Choose a skin from the css/skins
           folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/skins/_all-skins.min.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('bower_components/select2/dist/css/select2.min.css')); ?>">
<style>
    /* Quitar viñetas de la lista desplegable */
    .ui-autocomplete {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 200px; /* Ajustar la altura máxima de la lista */
        overflow-y: auto;  /* Agregar desplazamiento vertical si es necesario */
        z-index: 1000;     /* Asegurar que la lista se muestre encima de otros elementos */
    }

    /* Estilo de los elementos de la lista */
    .ui-menu-item {
        padding: 10px;
        border-bottom: 1px solid #ccc;
        background-color: #fff;
    }

    /* Estilo de los elementos cuando se hace hover o se seleccionan */
    .ui-menu-item:hover, .ui-menu-item.ui-state-focus {
        background-color: #f0f0f0;
    }
</style>

<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <i class="fa fa-tasks" aria-hidden="true"></i> Estados de la evaluación
                <small>Cambiar</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('viaje_evaluacions.index')); ?>">Evaluación Viajes/Estadías</a></li>
                <li><a href="<?php echo e(route('viaje_evaluacion_estados.index')); ?>">Estados</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php if($viaje_evaluacion): ?> Evaluación <?php echo e($viaje_evaluacion->viaje->periodo->nombre); ?> - <?php echo e($viaje_evaluacion->viaje->investigador->persona->apellido); ?> <?php echo e($viaje_evaluacion->viaje->investigador->persona->nombre); ?><?php endif; ?></h3>
                        </div>


                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="<?php echo e(route('viaje_evaluacion_estados.store')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo e(csrf_field()); ?>

                            <div class="box-body">
                                <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <!-- Nav tabs -->

                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    <?php echo e(Form::label('estado', 'Estado')); ?>

                                                    <?php echo e(Form::select('estado',['Creada'=>'Creada','Recibida'=>'Recibida','Aceptada'=>'Aceptada','Rechazada'=>'Rechazada','En evaluación'=>'En evaluación','Evaluada'=>'Evaluada','Rectificada'=>'Rectificada'], $viaje_evaluacion->estado,['class' => 'form-control'])); ?>

                                                </div>

                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="hidden" name="viaje_evaluacion_id" value="<?php echo e($viaje_evaluacion->id ?? ''); ?>">
                                                    <?php echo e(Form::label('periodo', 'Período')); ?>

                                                    <?php echo e(Form::text('periodo', $viaje_evaluacion->viaje->periodo->nombre, ['class' => 'form-control','placeholder'=>'Período','disabled'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('cuil', 'CUIL')); ?>

                                                    <?php echo e(Form::text('cuil', $viaje_evaluacion->viaje->investigador->persona->cuil, ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X','disabled'])); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('apellido', 'Apellido')); ?>

                                                    <?php echo e(Form::text('apellido', $viaje_evaluacion->viaje->investigador->persona->apellido, ['class' => 'form-control','placeholder'=>'Apellido','disabled'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('nombre', 'Nombre')); ?>

                                                    <?php echo e(Form::text('nombre', $viaje_evaluacion->viaje->investigador->persona->nombre, ['class' => 'form-control','placeholder'=>'Nombre','disabled'])); ?>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    <?php echo e(Form::label('comentarios', 'Comentarios')); ?>

                                                    <?php echo e(Form::textarea('comentarios', '', ['class' => 'form-control'])); ?>


                                                </div>
                                            </div>
                                        </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href="<?php echo e(route('viaje_evaluacion_estados.index')); ?>?viaje_evaluacion_id=<?php echo e($viaje_evaluacion->id); ?>" class="btn btn-warning">Volver</a>

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
    <!-- Inputmask -->
    <script src="<?php echo e(asset('bower_components/inputmask/dist/min/jquery.inputmask.bundle.min.js')); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo e(asset('dist/js/adminlte.min.js')); ?>"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo e(asset('dist/js/demo.js')); ?>"></script>
    <!-- jQuery UI -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script src="<?php echo e(asset('dist/js/confirm-exit.js')); ?>"></script>
    <!-- page script -->
    <script>
        $(document).ready(function () {
            $('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
            $('.js-example-basic-single').select2();

        });



    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/viaje_evaluacion_estados/create.blade.php ENDPATH**/ ?>