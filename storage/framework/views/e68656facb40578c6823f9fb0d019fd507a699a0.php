
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
                <i class="fa fa-user" aria-hidden="true"></i> Perfil
                <small>Editar</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <!--<li><a href="<?php echo e(route('users.index')); ?>">Usuarios</a></li>
                <li class="active">Edit Form</li>-->
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
                        <form role="form" action="<?php echo e(route('users.updatePerfil')); ?>" method="post" enctype="multipart/form-data">
                            <?php echo e(csrf_field()); ?>

                            <?php echo e(Form::hidden('idUser', Auth::user()->id)); ?>

                            <div class="box-body">

                                    <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-lg-6 col-md-3">
                                        <div class="form-group">
                                            <label for="name">Nombre</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Nombre" value="<?php if(old('name')): ?><?php echo e(old('name')); ?><?php else: ?><?php echo e(Auth::user()->name); ?><?php endif; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-4">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" id="email" name="email" placeholder="email" value="<?php if(old('email')): ?><?php echo e(old('email')); ?><?php else: ?><?php echo e(Auth::user()->email); ?><?php endif; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-4">
                                        <div class="form-group">
                                            <label for="cuil">CUIL</label>
                                            <input type="text" class="form-control" id="cuil" name="cuil" placeholder="XX-XXXXXXXX-X" value="<?php if(old('cuil')): ?><?php echo e(old('cuil')); ?><?php else: ?><?php echo e($user->cuil); ?><?php endif; ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-5">
                                        <div class="form-group">
                                            <label for="facultad_id">U. Académica</label>
                                            <select name="facultad_id" class="form-control js-example-basic-single">
                                                <option value="">Seleccionar UA</option>
                                                <?php $__currentLoopData = $facultades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $facultadId => $facultad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($facultadId); ?>" <?php echo e(old('facultad_id', $userFacultad) == $facultadId ? 'selected' : ''); ?>><?php echo e($facultad); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-2">
                                        <div class="form-group">
                                            <label for="password">Password</label>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="clave" value="<?php echo e(old('password')); ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-offset-3 col-lg-6 col-md-2">
                                        <div class="form-group">
                                            <label for="password_confirmation">Confirmar clave </label>
                                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirmar clave">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-offset-3 col-lg-6 col-md-5">
                                        <div class="form-group">
                                            <label for="foto">Foto</label>
                                            <?php if(Auth::user()->image): ?>
                                                <img id="original" src="<?php echo e(asset(Auth::user()->image)); ?>" height="200">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="delete_image" value="1">
                                                        Eliminar foto actual
                                                    </label>
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" name="image" class="form-control" placeholder="">

                                        </div>
                                    </div>

                                </div>


                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>

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
    <!-- Inputmask -->
    <script src="<?php echo e(asset('bower_components/inputmask/dist/min/jquery.inputmask.bundle.min.js')); ?>"></script>
    <!-- Select2 -->
    <script src="<?php echo e(asset('bower_components/select2/dist/js/select2.min.js')); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo e(asset('dist/js/adminlte.min.js')); ?>"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo e(asset('dist/js/demo.js')); ?>"></script>
    <!-- page script -->
    <script>
        $(document).ready(function () {
            $('#cuil').inputmask('99-99999999-9', { placeholder: 'XX-XXXXXXXX-X' });
            $('.js-example-basic-single').select2();
        });
    </script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/users/perfil.blade.php ENDPATH**/ ?>