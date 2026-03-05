
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
                <i class="fa fa-book" aria-hidden="true"></i>Evaluación Jóvenes Investigadores
                <small>Asignar Evaluadores</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('jovens.index')); ?>">Jóvenes Investigadores</a></li>
                <li><a href="<?php echo e(route('joven_evaluacions.index')); ?>">Evaluaciones</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title"><?php if($joven): ?> Solicitud <?php echo e($joven->periodo->nombre); ?> - <?php echo e($joven->investigador->persona->apellido); ?> <?php echo e($joven->investigador->persona->nombre); ?><?php endif; ?></h3>
                        </div>


                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="<?php echo e(route('joven_evaluacions.store')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo e(csrf_field()); ?>

                            <div class="box-body">
                                <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <!-- Nav tabs -->

                                        <div class="row">

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <input type="hidden" name="joven_id" value="<?php echo e($joven->id ?? ''); ?>">
                                                    <?php echo e(Form::label('periodo', 'Período')); ?>

                                                    <?php echo e(Form::text('periodo', $joven->periodo->nombre, ['class' => 'form-control','placeholder'=>'Período','disabled'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('cuil', 'CUIL')); ?>

                                                    <?php echo e(Form::text('cuil', $joven->investigador->persona->cuil, ['class' => 'form-control','placeholder'=>'XX-XXXXXXXX-X','disabled'])); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('apellido', 'Apellido')); ?>

                                                    <?php echo e(Form::text('apellido', $joven->investigador->persona->apellido, ['class' => 'form-control','placeholder'=>'Apellido','disabled'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('nombre', 'Nombre')); ?>

                                                    <?php echo e(Form::text('nombre', $joven->investigador->persona->nombre, ['class' => 'form-control','placeholder'=>'Nombre','disabled'])); ?>

                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">

                                            <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                                <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Evaluadores internos</legend>

                                                <div class="form-group col-md-12">

                                                    <div class="table-responsive">
                                                        <table class="table" style="width: 50%">
                                                            <thead>

                                                            <th>Evaluador</th>

                                                            <th><a href="#" class="addRowInterno"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                            </thead>

                                                            <tbody id="cuerpoInternos">
                                                            <?php $__currentLoopData = $joven->evaluacions->where('interno', 1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $interno): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <tr>

                                                                    <td><?php echo e(Form::hidden('internos_id[]',$interno->id)); ?><?php echo e(Form::select('internos[]',$internos, $interno->user_id,['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px'])); ?></td>

                                                                    <td><a href="#" class="btn btn-danger removeInterno"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                                </tr>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                            </tbody>




                                                        </table>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        </div>
                                <div class="row">

                                    <fieldset style="border: 1px solid #ccc; padding: 10px;">
                                        <legend style="border-bottom: none; margin-bottom: -10px; display: inline-block;width: auto;">Evaluadores externos</legend>

                                        <div class="form-group col-md-12">

                                            <div class="table-responsive">
                                                <table class="table" style="width: 50%">
                                                    <thead>

                                                    <th>Evaluador</th>

                                                    <th><a href="#" class="addRowExterno"><i class="glyphicon glyphicon-plus"></i></a></th>

                                                    </thead>

                                                    <tbody id="cuerpoExternos">
                                                    <?php $__currentLoopData = $joven->evaluacions->where('interno', 0); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $externo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                                                        <tr>

                                                            <td><?php echo e(Form::hidden('externos_id[]',$externo->id)); ?><?php echo e(Form::select('externos[]',$externos, $externo->user_id,['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px'])); ?></td>

                                                            <td><a href="#" class="btn btn-danger removeExterno"><i class="glyphicon glyphicon-remove"></i></a></td>
                                                        </tr>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                    </tbody>




                                                </table>
                                            </div>
                                        </div>
                                    </fieldset>
                                </div>
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href="<?php echo e(route('joven_evaluacions.index')); ?>?joven_id=<?php echo e($joven->id); ?>" class="btn btn-warning">Volver</a>

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
            $('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
            $('.js-example-basic-single').select2();






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

            $('.addRowInterno').on('click',function(e){
                e.preventDefault();
                addRowInterno();
            });
            function addRowInterno()
            {
                var tr='<tr>'+
                    '<td>'+'<?php echo e(Form::select('internos[]',$internos ?? [''=>''], '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px'])); ?>'+'</td>'+


                    '<td><a href="#" class="btn btn-danger removeInterno"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                    '</tr>';
                $('#cuerpoInternos').append(tr);
                $('.js-example-basic-single').select2();
            };

            $('body').on('click', '.removeInterno', function(e){

                e.preventDefault();
                var confirmDelete = confirm('¿Estás seguro?');

                if (confirmDelete) {
                    $(this).parent().parent().remove();
                }



            });

            $('.addRowExterno').on('click',function(e){
                e.preventDefault();
                addRowExterno();
            });
            function addRowExterno()
            {
                var tr='<tr>'+
                    '<td>'+'<?php echo e(Form::select('externos[]',$externos ?? [''=>''], '',['class' => 'form-control js-example-basic-single', 'style' => 'width: 300px'])); ?>'+'</td>'+


                    '<td><a href="#" class="btn btn-danger removeExterno"><i class="glyphicon glyphicon-remove"></i></a></td>'+
                    '</tr>';
                $('#cuerpoExternos').append(tr);
                $('.js-example-basic-single').select2();
            };

            $('body').on('click', '.removeExterno', function(e){

                e.preventDefault();
                var confirmDelete = confirm('¿Estás seguro?');

                if (confirmDelete) {
                    $(this).parent().parent().remove();
                }



            });

        });



    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/joven_evaluacions/create.blade.php ENDPATH**/ ?>