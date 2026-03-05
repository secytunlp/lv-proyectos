<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Resetear clave</title>
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
</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <b>Plataforma de carga de datos</b><br>SECyT UNLP
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">

        <p class="login-box-msg"><?php echo e(__('passwords.reset_password')); ?>

        </p>
        <?php echo $__env->make('includes.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                <div class="card-body">
                    <?php if(session('status')): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo e(session('status')); ?>

                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?php echo e(route('password.email')); ?>">
                        <?php echo csrf_field(); ?>

                        <div class="form-group has-feedback">
                                <input id="email" type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="email" autofocus placeholder="Email">
                                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>

                            </div>


                        <div class="row mb-0">
                            <div class="col-xs-8">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo e(__('passwords.send_link')); ?>

                                </button>
                            </div>
                            <!-- /.col -->
                            <div class="col-xs-4">
                                <a href="<?php echo e(route('login')); ?>" class="btn btn-info">Volver</a>
                            </div>
                            <!-- /.col -->

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
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
<?php /**PATH /var/www/sicadi/resources/views/auth/passwords/email.blade.php ENDPATH**/ ?>