<!DOCTYPE html>
<html lang="en">

<head>

    <?php echo $__env->make('layouts.partials.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

    <?php echo $__env->make('layouts.partials.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->make('layouts.partials.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <?php echo $__env->yieldContent('content'); ?>

    <?php echo $__env->yieldSection(); ?>


    <?php echo $__env->make('layouts.partials.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

</div>
<!-- ./wrapper -->

</body>
</html>
<?php /**PATH /var/www/sicadi/resources/views/layouts/app.blade.php ENDPATH**/ ?>