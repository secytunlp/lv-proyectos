<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Envío de Solicitud</title>
</head>
<body style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; color: #666666; background-color: #FFFFFF; padding: 30px;">
<img src="<?php echo e(url('/images/secyt.gif')); ?>" alt="Logo" style="margin-bottom: 20px;">
<h2 style="color: #333333;">PROYECTOS DE INVESTIGACIÓN</h2>
<hr style="color: #999999; text-decoration: none;">
<p>
    <strong><?php echo e($asunto); ?></strong><br>
    <strong>Proyecto</strong>: <?php echo e($codigo); ?><br>
    <strong><?php echo e($integranteMail); ?></strong>: <?php echo e($integrante); ?><br>
    <strong>F. de <?php echo e($tipo); ?></strong>: <?php echo e($fecha); ?><br>
    <?php echo e($comment); ?>

</p>
<hr style="color: #999999; text-decoration: none;">
</body>
</html>

<?php /**PATH /var/www/sicadi/resources/views/emails/solicitud.blade.php ENDPATH**/ ?>