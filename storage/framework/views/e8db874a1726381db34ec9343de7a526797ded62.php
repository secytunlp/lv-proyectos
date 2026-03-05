
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
                <i class="fa fa-user-friends" aria-hidden="true"></i>Proyecto
                <small>Ver</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('proyectos.index')); ?>">Proyectos</a></li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <!-- general form elements -->
                    <div class="box box-primary">



                        <!-- /.box-header -->
                        <!-- form start -->
                        <form role="form" action="<?php echo e(route('integrantes.update',$proyecto->id)); ?>" method="post" enctype="multipart/form-data">
                            <?php echo e(csrf_field()); ?>

                            <?php echo e(method_field('PUT')); ?>

                            <div class="box-body">
                                <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#datos_proyecto" aria-controls="datos_proyecto" role="tab" data-toggle="tab">Proyecto</a></li>
                                    <li role="presentation"><a href="#investigacion" aria-controls="investigacion" role="tab" data-toggle="tab">Investigación</a></li>

                                    <li role="presentation"><a href="#resumen" aria-controls="resumen" role="tab" data-toggle="tab">Resumen</a></li>
                                    <!-- Agrega más pestañas según sea necesario -->
                                </ul>
                                <div class="tab-content" style="margin: 1%;">
                                    <div role="tabpanel" class="tab-pane active" id="datos_proyecto">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    <?php echo e(Form::label('tipo', 'Tipo')); ?>

                                                    <?php echo e(Form::text('tipo', $proyecto->tipo, ['class' => 'form-control','disabled'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">

                                                    <?php echo e(Form::label('codigo', 'Código')); ?>

                                                    <?php echo e(Form::text('codigo', $proyecto->codigo, ['class' => 'form-control','disabled'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('sigeva', 'Código SIGEVA')); ?>

                                                    <?php echo e(Form::text('sigeva', $proyecto->sigeva, ['class' => 'form-control','disabled'])); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    <?php echo e(Form::label('titulo', 'Título')); ?>

                                                    <?php echo e(Form::textarea('titulo', $proyecto->titulo, ['class' => 'form-control','disabled', 'rows' => 3])); ?>


                                                </div>
                                            </div>



                                        </div>
                                        <div class="row">

                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('director', 'Director')); ?>

                                                    <?php echo e(Form::text('director', $director->director_apellido, ['class' => 'form-control','disabled'])); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-2">

                                                <div class="form-group">
                                                    <?php echo e(Form::label('inicio', 'Fecha de inicio')); ?>

                                                    <?php echo e(Form::date('inicio', ($proyecto->inicio)?date('Y-m-d', strtotime($proyecto->inicio)):'', ['class' => 'form-control','disabled'])); ?>


                                                </div>
                                            </div>
                                            <div class="col-md-2">

                                                <div class="form-group">
                                                    <?php echo e(Form::label('fin', 'Fecha de fin')); ?>

                                                    <?php echo e(Form::date('fin', ($proyecto->fin)?date('Y-m-d', strtotime($proyecto->fin)):'', ['class' => 'form-control','disabled'])); ?>


                                                </div>
                                            </div>

                                            <div class="col-md-2">

                                                <div class="form-group">
                                                    <?php echo e(Form::label('estado', 'Estado')); ?>

                                                    <?php echo e(Form::text('estado', $proyecto->estado, ['class' => 'form-control','disabled'])); ?>


                                                </div>
                                            </div>

                                        </div>

                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="investigacion">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('facultad', 'U. Académica')); ?>

                                                    <?php echo e(Form::select('facultad_id',  $facultades,$proyecto->facultad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'facultad_id','disabled'])); ?>


                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('unidad', 'Unidad de investigación')); ?>

                                                    <?php echo e(Form::select('unidad_id',  $unidads,$proyecto->unidad_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'unidad_id','disabled'])); ?>


                                                </div>
                                            </div>


                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <?php
                                                    $especialidad = $proyecto->disciplina()->nombre;
                                                    if ($proyecto->especialidad()){
                                                        $especialidad .= ' - '.$proyecto->especialidad()->nombre;
                                                    }
                                                    ?>
                                                    <?php echo e(Form::label('especialidad', 'Especialidad')); ?>


                                                    <?php echo e(Form::text('especialidad', $especialidad, ['class' => 'form-control','disabled'])); ?>

                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('tipoinv', 'Tipo de investigación')); ?>

                                                    <?php echo e(Form::text('tipoinv', $proyecto->investigacion, ['class' => 'form-control','disabled'])); ?>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">

                                                    <?php echo e(Form::label('campo', 'Campo de aplicación')); ?>


                                                    <?php echo e(Form::select('campo_id',  $campos,$proyecto->campo_id, ['class' => 'form-control js-example-basic-single', 'style' => 'width: 100%','id'=>'campo_id','disabled'])); ?>

                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <?php echo e(Form::label('linea', 'Línea de investigación')); ?>

                                                    <?php echo e(Form::text('linea', $proyecto->linea, ['class' => 'form-control','disabled'])); ?>

                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div role="tabpanel" class="tab-pane" id="resumen">
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    <?php echo e(Form::label('abstract', 'Resumen')); ?>

                                                    <?php echo e(Form::textarea('abstract', $proyecto->resumen, ['class' => 'form-control','disabled'])); ?>


                                                </div>
                                            </div>



                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    <?php echo e(Form::label('claves', 'Palabras claves')); ?>


                                                    <?php echo e(Form::text('claves', $proyecto->clave1.' - '.$proyecto->clave2.' - '.$proyecto->clave3.' - '.$proyecto->clave4.' - '.$proyecto->clave5.' - '.$proyecto->clave6, ['class' => 'form-control','disabled'])); ?>

                                                </div>
                                            </div>



                                        </div>
                                        <div class="row">

                                            <div class="col-md-8">

                                                <div class="form-group">
                                                    <?php echo e(Form::label('keys', 'Palabras claves en inglés')); ?>


                                                    <?php echo e(Form::text('keys', $proyecto->key1.' - '.$proyecto->key2.' - '.$proyecto->key3.' - '.$proyecto->key4.' - '.$proyecto->key5.' - '.$proyecto->key6, ['class' => 'form-control','disabled'])); ?>

                                                </div>
                                            </div>



                                        </div>

                                    </div>
                                </div>
                                    <div class="form-group">
                                        <!--<button type="submit" class="btn btn-primary">Guardar</button>-->
                                        <a href="<?php echo e(route('proyectos.index')); ?>" class="btn btn-warning">Volver</a>

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




<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/proyectos/show.blade.php ENDPATH**/ ?>