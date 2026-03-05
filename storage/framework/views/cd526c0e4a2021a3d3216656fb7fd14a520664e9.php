
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
                <i class="fa fa-user-friends" aria-hidden="true"></i>Integrante
                <small>Rechazar Alta</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('integrantes.index')); ?>">Integrantes</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php if($proyecto): ?> <?php echo e($proyecto->codigo); ?> <?php echo e($proyecto->titulo); ?><?php endif; ?></h3>
                        </div>


                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="<?php echo e(route('integrantes.saveDeny',$integrante->id)); ?>" method="post" enctype="multipart/form-data">
                            <?php echo e(csrf_field()); ?>

                            <?php echo e(method_field('PUT')); ?>

                            <div class="box-body">
                                <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <!-- Nav tabs -->


                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <input type="hidden" name="proyecto_id" value="<?php echo e($proyecto->id ?? ''); ?>">
                                                    <input type="hidden" name="alta" value="<?php echo e(($integrante->alta)?date('Y-m-d', strtotime($integrante->alta)):''); ?>">
                                                    <?php echo e(Form::label('tipo', 'Tipo')); ?>

                                                    <?php echo e(Form::select('tipo',[''=>'','Investigador Formado'=>'Investigador Formado','Investigador En Formación'=>'Investigador En Formación','Becario, Tesista'=>'Becario, Tesista','Colaborador'=>'Colaborador'], $integrante->tipo,['class' => 'form-control','disabled'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('estado', 'Estado')); ?>

                                                    <?php echo e(Form::text('estado', $integrante->estado, ['class' => 'form-control','placeholder'=>'Estado','disabled'])); ?>

                                                </div>
                                            </div>
                                        </div>



                                        <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?php echo e(Form::label('apellido', 'Apellido')); ?>

                                                <?php echo e(Form::text('apellido', $integrante->investigador->persona->apellido, ['class' => 'form-control','placeholder'=>'Apellido','disabled'])); ?>

                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <?php echo e(Form::label('nombre', 'Nombre')); ?>

                                                <?php echo e(Form::text('nombre', $integrante->investigador->persona->nombre, ['class' => 'form-control','placeholder'=>'Nombre','disabled'])); ?>

                                            </div>
                                        </div>


                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <?php echo e(Form::label('cuil', 'CUIL')); ?>

                                                <?php echo e(Form::text('cuil', $integrante->investigador->persona->cuil, ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X','disabled'])); ?>

                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <?php echo e(Form::label('email', 'Email')); ?>

                                                <?php echo e(Form::email('email', $integrante->email, ['class' => 'form-control','placeholder'=>'Email','disabled'])); ?>

                                            </div>
                                        </div>

                                    </div>


                                <div class="row">

                                    <div class="col-md-8">

                                        <div class="form-group">
                                            <?php echo e(Form::label('comentarios', 'Motivos')); ?>

                                            <?php echo e(Form::textarea('comentarios', '', ['class' => 'form-control'])); ?>


                                        </div>
                                    </div>
                                </div>




                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href="<?php echo e(route('integrantes.index')); ?>?proyecto_id=<?php echo e($proyecto->id); ?>" class="btn btn-warning">Volver</a>

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
    <!-- page script -->
    <script>
        $(document).ready(function () {




            // Bandera para rastrear si se han realizado cambios en el formulario
            var cambiosRealizados = false;

            // Escuchar cambios en los campos del formulario
            $('input, select, textarea').on('change', function() {
                cambiosRealizados = true;
            });

            // Mostrar mensaje de advertencia cuando el usuario intente abandonar la página
            window.addEventListener('beforeunload', function (event) {
                // Verificar si se han realizado cambios en el formulario
                if (cambiosRealizados) {
                    // Personaliza el mensaje de advertencia según tus necesidades
                    var mensaje = "¡Atención! Puede perder algunos cambios. ¿Estás seguro de abandonar la página?";

                    // Establecer el mensaje de advertencia en el evento
                    event.returnValue = mensaje;

                    // Devolver el mensaje de advertencia (solo necesario en algunos navegadores antiguos)
                    return mensaje;
                }
            });

            // Escuchar el envío del formulario
            $('form').on('submit', function() {
                // Establecer cambios realizados como false
                cambiosRealizados = false;
            });

        });



    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/integrantes/deny.blade.php ENDPATH**/ ?>