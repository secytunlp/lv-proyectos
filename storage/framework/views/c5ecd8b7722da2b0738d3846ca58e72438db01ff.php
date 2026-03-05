
<?php $__env->startSection('headSection'); ?>

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
                <i class="fa fa-user-plus" aria-hidden="true"></i> Rol
                <small>Editar</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="<?php echo e(route('home')); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="<?php echo e(route('roles.index')); ?>">Roles</a></li>
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
                        <form role="form" action="<?php echo e(route('roles.update',$role->id)); ?>" method="post" >
                            <?php echo e(csrf_field()); ?>

                            <?php echo e(method_field('PUT')); ?>

                            <div class="box-body">

                                    <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                <div class="row">
                                    <div class="col-lg-offset-3 col-lg-6 col-md-3">
                                        <div class="form-group">
                                            <label for="name">Nombre</label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Nombre" value="<?php if(old('name')): ?><?php echo e(old('name')); ?><?php else: ?><?php echo e($role->name); ?><?php endif; ?>">
                                        </div>
                                    </div>
                                </div>

            <div class="form-group">
                <label for="permisos">Permisos</label>

                <div class="row">

                <?php $__currentLoopData = $permission; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <div class="col-md-3">
                            <label><input class="name" type="checkbox" name="permission[]" value="<?php echo e($value->id); ?>"
                                          <?php $__currentLoopData = $role->permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role_permit): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                          <?php if($role_permit->id == $value->id): ?>
                                          checked
                                    <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                ><?php echo e($value->name); ?></label>
                        </div>


                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </div>
            </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">Guardar</button>
                                        <a href='<?php echo e(route('roles.index')); ?>' class="btn btn-warning">Volver</a>
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
    <!-- FastClick -->
    <script src="<?php echo e(asset('bower_components/fastclick/lib/fastclick.js')); ?>"></script>
    <!-- AdminLTE App -->
    <script src="<?php echo e(asset('dist/js/adminlte.min.js')); ?>"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="<?php echo e(asset('dist/js/demo.js')); ?>"></script>
    <!-- page script -->

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/sicadi/resources/views/roles/edit.blade.php ENDPATH**/ ?>