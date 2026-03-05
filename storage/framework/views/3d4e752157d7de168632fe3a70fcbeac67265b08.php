<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Seleccionar Rol</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="icon" href="<?php echo e(asset('images/favicon.ico')); ?>" type="image/x-icon">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?php echo e(asset('bower_components/bootstrap/dist/css/bootstrap.min.css')); ?>">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?php echo e(asset('bower_components/font-awesome/css/font-awesome.min.css')); ?>">
    <!-- Ionicons -->
    <link rel="stylesheet" href="<?php echo e(asset('bower_components/Ionicons/css/ionicons.min.css')); ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?php echo e(asset('dist/css/AdminLTE.min.css')); ?>">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?php echo e(asset('plugins/iCheck/square/blue.css')); ?>">
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <!-- Custom Styles -->
    <style>
        .login-box .login-links a {
            display: block;
            margin-bottom: 10px;
        }
        .login-box .login-links a.btn {
            width: 100%;
        }
    </style>
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Plataforma de carga de datos</b><br>SECyT UNLP
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Seleccionar rol</p>
        <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <form method="POST" action="<?php echo e(route('save-selected-rol')); ?>">
            <?php echo csrf_field(); ?>

            <div class="form-group">
                <label for="role">Seleccione el rol:</label>
                <select name="rol_id" id="role" class="form-control">
                    <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($role->id); ?>"><?php echo e($role->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Seleccionar</button>
        </form>



    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="<?php echo e(asset('bower_components/jquery/dist/jquery.min.js')); ?>"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo e(asset('bower_components/bootstrap/dist/js/bootstrap.min.js')); ?>"></script>
<div class="row">
    <div class="col-xs-12">
        <div style="text-align: center">
            <img src="<?php echo e(url('/images/secyt_unlp.PNG')); ?>" >
        </div>

    </div>
</div>
</body>
</html>
<?php /**PATH /var/www/sicadi/resources/views/users/select-rol.blade.php ENDPATH**/ ?>