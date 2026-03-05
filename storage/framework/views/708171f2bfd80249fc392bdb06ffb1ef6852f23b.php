<!DOCTYPE html>
<html>
<head>
    <title>Solicitud</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            margin-top: 100px; /* Asegúrate de dejar espacio para el header */
            padding: 0;
        }
        .header {
            display: table; /* Utiliza display: table para alinear la imagen y el texto */
            width: 100%;
            border: 1px solid #000; /* Borde alrededor del encabezado */
            padding: 10px; /* Espacio dentro del borde */
            margin-bottom: 10px;
            box-sizing: border-box; /* Asegura que el padding y el borde estén incluidos en el ancho total */
            position: fixed;
            top: 0px; /* Asegúrate de que se mantenga en la parte superior de la página */
            left: 0px;
            right: 0px;
        }
        .logo {
            display: table-cell; /* Celda de la tabla para la imagen */
            width: 340px;
            height: 64px;
            vertical-align: top;
        }

        .text-content {
            display: flex;
            flex-direction: column; /* Stack text elements vertically */
            justify-content: center; /* Center text content vertically within the flex container */


            margin-left: auto; /* Empuja el contenido hacia la derecha */
            width: 100%; /* Asegura que el texto ocupa el espacio disponible */
            text-align: right; /* Alinea el texto a la derecha dentro de .text-content */
        }

        .title {
            font-size: 15px; /* Title font size */
        }

        .subtitle {
            font-size: 13px; /* Subtitle font size */
            margin-top: 5px; /* Space between title and subtitle */
        }
        .section-title { background-color: #cccccc; /*padding: 5px;*/ font-weight: bold; }
        .content { margin-bottom: 10px;}
        .signature { margin-top: 50px; text-align: center; }
        .signature div { display: inline-block; width: 40%; text-align: center; }
        .signature-line {
            border-top: 1px solid black;
            padding-top: 5px;
            margin-top: 20px;
        }
        .content-border {
            border: 1px solid #ccc; /* Borde alrededor del texto */
            padding: 5px; /* Ajustar el relleno según necesites */
            margin-top: 10px;
            margin-left: 5px;
            display: inline-block; /* Para que el borde se ajuste al contenido */
            width: 535px; /* Extender el span al ancho completo del contenedor */
            box-sizing: border-box; /* Incluir el borde y el relleno en el ancho total */
            text-align: left; /* Alinear texto a la izquierda dentro del borde */
        }
        .unit-title {
            background-color: black;
            color: white;
            text-align: right;
            /*padding: 5px;*/
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .unit-title span {
            float: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
            margin-top: 10px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        .watermark {
            position: fixed;
            top: 50%; /* Centrar verticalmente */
            left: 50%; /* Centrar horizontalmente */
            transform: translate(-50%, -50%) rotate(-45deg); /* Rotación diagonal y centrado */
            opacity: 0.4; /* Opacidad ajustada para que se note */
            font-size: 80px; /* Tamaño del texto */
            color: #cccccc; /* Color de la marca de agua */
            pointer-events: none; /* Evita que la marca de agua sea interactiva */
            z-index: -1; /* Coloca la marca de agua detrás del contenido */
        }

         .page-break {
             page-break-after: always;
         }



    </style>
</head>
<body>
<?php //dd($objetivo); ?>
<?php if ($estado === 'Creada'): ?>
    <div class="watermark">VISTA_PRELIMINAR</div>
<?php endif;
$motivoLetra = '';

switch ($motivo) {
    case 'A) Reuniones Científicas':
        $motivoLetra='A';
        break;
    case 'B) Estadía de trabajo para investigar en ámbitos académicos externos a la UNLP':
        $motivoLetra='B';
        break;
    case 'C) ESTADÍA DE TRABAJO EN LA UNLP PARA UN INVESTIGADOR INVITADO':
        $motivoLetra='C';
        break;

}

?>
<div class="header">
    <div class="logo">

        <img src="<?php echo e(public_path('/images/unlp.png')); ?>" alt="UNLP Logo" class="logo">

    </div>
    <div class="text-content">
        <div class="title">
            SOLICITUD DE SUBSIDIOS <?php echo e($year); ?>

        </div>
        <div class="subtitle">
            Viajes/Estadías
        </div>
        <div class="subtitle">
            <?php echo e($mes_desde); ?> <?php echo e($year); ?> - <?php echo e($mes_hasta); ?> <?php echo e(intval($year)+1); ?>

        </div>
    </div>
</div>



<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Apellido y Nombres</span> <span style="display: inline-block; border: 1px solid #ccc;width: 350px; padding-top: <?php echo e($solicitante ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($solicitante); ?></span><span style="display: inline-block;width: 55px;"></span>
        <span style="display: inline-block;width: 40px;">CUIL</span> <span style="display: inline-block; border: 1px solid #ccc;width: 110px; padding-top: <?php echo e($cuil ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($cuil); ?></span>
    </div>

</div>

<div class="content">
    <div>Domicilio de notificación (Dentro del Radio Urbano de La Plata, Art. 20 Ord. 101)</div>
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 60px;">Calle</span> <span style="display: inline-block; border: 1px solid #ccc;width: 255px; padding-top: <?php echo e($calle ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($calle); ?></span>
        <span style="display: inline-block;width: 35px;">Nro.</span> <span style="display: inline-block; border: 1px solid #ccc;width: 45px; padding-top: <?php echo e($nro ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($nro); ?></span>
        <span style="display: inline-block;width: 35px;">Piso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 45px; padding-top: <?php echo e($piso ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($piso); ?></span>
        <span style="display: inline-block;width: 40px;">Dpto.</span> <span style="display: inline-block; border: 1px solid #ccc;width: 45px; padding-top: <?php echo e($depto ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($depto); ?></span>
        <span style="display: inline-block;width: 35px;">C.P.</span> <span style="display: inline-block; border: 1px solid #ccc;width: 60px; padding-top: <?php echo e($cp ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($cp); ?></span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 50px;">E-mail</span> <span style="display: inline-block; border: 1px solid #ccc;width: 420px; padding-top: <?php echo e($email ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($email); ?></span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 60px;">Teléfono</span> <span style="display: inline-block; border: 1px solid #ccc;width: 120px; padding-top: <?php echo e($telefono ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($telefono); ?></span>
    </div>
    <?php if(intval($year)>2012): ?>
    <div style="font-size: 10px;">Acepto recibir toda notificación relativa a la presente solicitud en la dirección de correo electrónico declarada
        precedentemente <?php echo e($notificacion); ?></div>
    <?php endif; ?>
</div>
<?php if(intval($year)>2018): ?>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 200px;">Link del perfil google scholar</span> <span style="display: inline-block; border: 1px solid #ccc;width: 495px; padding-top: <?php echo e($scholar ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($scholar); ?></span>

    </div>

</div>
<?php endif; ?>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Título de grado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 350px; padding-top: <?php echo e($titulo ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($titulo); ?></span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 70px;">F. Egreso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($egreso ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($egreso)?date('d/m/Y', strtotime($egreso)):''); ?></span>
    </div>

</div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 230px;">Lugar de Trabajo de Inv. en la UNLP</span> <span style="display: inline-block; border: 1px solid #ccc;width: 465px; padding-top: <?php echo e($unidad ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($unidad); ?></span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 70px;">Dirección</span> <span style="display: inline-block; border: 1px solid #ccc;width: 400px; padding-top: <?php echo e($direccion_unidad ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($direccion_unidad); ?></span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 60px;">Teléfono</span> <span style="display: inline-block; border: 1px solid #ccc;width: 120px; padding-top: <?php echo e($telefono_unidad ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($telefono_unidad); ?></span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 150px;">Cargo docente UNLP</span> <span style="display: inline-block; border: 1px solid #ccc;width: 300px; padding-top: <?php echo e($cargo ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($cargo); ?></span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 70px;">Dedicación</span> <span style="display: inline-block; border: 1px solid #ccc;width: 130px; padding-top: <?php echo e($dedicacion ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($dedicacion); ?></span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 70px;">Facultad</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: <?php echo e($facultad ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($facultad); ?></span>

    </div>

</div>
<?php if(!empty($carrerainv)): ?>
    <div class="content">
        <div>INVESTIGADOR DE CARRERA</div>
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 70px;">Institución</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: <?php echo e($organismo ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($organismo); ?></span>

        </div>

    </div>
    <div class="content">
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 100px;">Categoría</span> <span style="display: inline-block; border: 1px solid #ccc;width: 230px; padding-top: <?php echo e($carrerainv ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($carrerainv); ?></span><span style="display: inline-block;width: 105px;"></span>
            <span style="display: inline-block;width: 50px;">Ingreso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 200px; padding-top: <?php echo e($ingreso_carrera ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($ingreso_carrera)?date('d/m/Y', strtotime($ingreso_carrera)):''); ?> </span>
        </div>

    </div>
    <div class="content">
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 130px;">Lugar de Trabajo</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: <?php echo e($unidadcarrera ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($unidadcarrera); ?></span>

        </div>

    </div>

<?php endif; ?>
<?php if(!empty($beca_beca)): ?>
    <div class="content">
        <div>BECARIO</div>
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 70px;">Institución</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: <?php echo e($beca_institucion ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($beca_institucion); ?></span>

        </div>

    </div>
    <div class="content">
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 100px;">Nivel de beca</span> <span style="display: inline-block; border: 1px solid #ccc;width: 230px; padding-top: <?php echo e($beca_beca ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($beca_beca); ?></span><span style="display: inline-block;width: 105px;"></span>
            <span style="display: inline-block;width: 50px;">Período</span> <span style="display: inline-block; border: 1px solid #ccc;width: 200px; padding-top: <?php echo e($beca_periodo ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($beca_periodo); ?></span>
        </div>

    </div>
    <div class="content">
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 130px;">Lugar de Trabajo</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: <?php echo e($unidadbeca ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($unidadbeca); ?></span>

        </div>

    </div>
    <?php if(intval($year)>2017): ?>
        <div class="content">
            <div>RESUMEN DE LA BECA</div>
        </div>

        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($resumen_beca ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $resumen_beca)); ?></span>
        </div>
    <?php endif; ?>
<?php endif; ?>
<div class="content">

    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100px;">Categoría SPU</span> <span style="display: inline-block; border: 1px solid #ccc;width: 50px; padding-top: <?php echo e($categoria ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($categoria); ?></span>
        <span style="display: inline-block;width: 120px;">Categoría SICADI</span> <span style="display: inline-block; border: 1px solid #ccc;width: 50px; padding-top: <?php echo e($sicadi ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($sicadi); ?></span>
        <span style="display: inline-block;width: 20px;"></span>
        <span style="display: inline-block;width: 80px;">Postulante</span> <span style="display: inline-block; border: 1px solid #ccc;width: 250px; padding-top: <?php echo e($tipo ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($tipo); ?></span>

    </div>

</div>

<?php if(!empty($proyectosActuales)): ?>
    <?php if(intval($year)>2016): ?>
        <div class="content">
            <div>PROYECTO DE INVESTIGACION SELECCIONADO EN EL MARCO DEL CUAL SE REALIZARA LA ACTIVIDAD</div>


        </div>
        <?php $__currentLoopData = $proyectosActuales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proyectoActual): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($proyectoActual['seleccionado']): ?>
                <div class="content">

                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Título</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: <?php echo e($proyectosActuales[0]['titulo'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['titulo']); ?></span>

                    </div>

                </div>
                <div class="content">
                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Director</span> <span style="display: inline-block; border: 1px solid #ccc;width: 400px; padding-top: <?php echo e($proyectoActual['director'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['director']); ?></span><span style="display: inline-block;width: 35px;"></span>
                        <span style="display: inline-block;width: 60px;">Estado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 120px; padding-top: <?php echo e($proyectoActual['estado'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['estado']); ?></span>
                    </div>

                </div>
                <div class="content">

                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Código</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($proyectoActual['codigo'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['codigo']); ?></span>
                        <span style="display: inline-block;width: 90px;"></span>
                        <span style="display: inline-block;width: 60px;">Desde</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($proyectoActual['inicio'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($proyectoActual['inicio']) ? date('d/m/Y', strtotime($proyectoActual['inicio'])) : ''); ?></span>
                        <span style="display: inline-block;width: 85px;"></span>
                        <span style="display: inline-block;width: 60px;">Hasta</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($proyectoActual['fin'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($proyectoActual['fin']) ? date('d/m/Y', strtotime($proyectoActual['fin'])) : ''); ?></span>

                    </div>

                </div>
                <div class="content">
                    <div>Resumen</div>
                </div>

                <div class="content">
                    <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($proyectoActual['resumen'] ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $proyectoActual['resumen'])); ?></span>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php if(count($proyectosActuales)>1): ?>
                <div class="content">
                    <div>OTROS PROYECTOS EN LOS QUE PARTICIPA</div>


                </div>
            <?php endif; ?>
        <?php $__currentLoopData = $proyectosActuales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proyectoActual): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!$proyectoActual['seleccionado']): ?>
                        <div class="content">

                            <div class="content" style="margin-top: 10px;">
                                <span style="display: inline-block;width: 70px;">Título</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: <?php echo e($proyectosActuales[0]['titulo'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['titulo']); ?></span>

                            </div>

                        </div>
                        <div class="content">
                            <div class="content" style="margin-top: 10px;">
                                <span style="display: inline-block;width: 70px;">Director</span> <span style="display: inline-block; border: 1px solid #ccc;width: 400px; padding-top: <?php echo e($proyectoActual['director'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['director']); ?></span><span style="display: inline-block;width: 35px;"></span>
                                <span style="display: inline-block;width: 60px;">Estado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 120px; padding-top: <?php echo e($proyectoActual['estado'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['estado']); ?></span>
                            </div>

                        </div>
                        <div class="content">

                            <div class="content" style="margin-top: 10px;">
                                <span style="display: inline-block;width: 70px;">Código</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($proyectoActual['codigo'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['codigo']); ?></span>
                                <span style="display: inline-block;width: 90px;"></span>
                                <span style="display: inline-block;width: 60px;">Desde</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($proyectoActual['inicio'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($proyectoActual['inicio']) ? date('d/m/Y', strtotime($proyectoActual['inicio'])) : ''); ?></span>
                                <span style="display: inline-block;width: 85px;"></span>
                                <span style="display: inline-block;width: 60px;">Hasta</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($proyectoActual['fin'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($proyectoActual['fin']) ? date('d/m/Y', strtotime($proyectoActual['fin'])) : ''); ?></span>

                            </div>

                        </div>
                        <div class="content">
                            <div>Resumen</div>
                        </div>

                        <div class="content">
                            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($proyectoActual['resumen'] ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $proyectoActual['resumen'])); ?></span>
                        </div>

                <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php else: ?>
        <div class="content">
            <div>PROYECTO/S ACREDITADO/S EN EL/LOS QUE PARTICIPA ACTUALMENTE SELECCIONADO/S</div>


        </div>
        <?php $__currentLoopData = $proyectosActuales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proyectoActual): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                <div class="content">

                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Título</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: <?php echo e($proyectosActuales[0]['titulo'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['titulo']); ?></span>

                    </div>

                </div>
                <div class="content">
                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Director</span> <span style="display: inline-block; border: 1px solid #ccc;width: 400px; padding-top: <?php echo e($proyectoActual['director'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['director']); ?></span><span style="display: inline-block;width: 35px;"></span>
                        <span style="display: inline-block;width: 60px;">Estado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 120px; padding-top: <?php echo e($proyectoActual['estado'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['estado']); ?></span>
                    </div>

                </div>
                <div class="content">

                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Código</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($proyectoActual['codigo'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($proyectoActual['codigo']); ?></span>
                        <span style="display: inline-block;width: 90px;"></span>
                        <span style="display: inline-block;width: 60px;">Desde</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($proyectoActual['inicio'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($proyectoActual['inicio']) ? date('d/m/Y', strtotime($proyectoActual['inicio'])) : ''); ?></span>
                        <span style="display: inline-block;width: 85px;"></span>
                        <span style="display: inline-block;width: 60px;">Hasta</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($proyectoActual['fin'] ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($proyectoActual['fin']) ? date('d/m/Y', strtotime($proyectoActual['fin'])) : ''); ?></span>

                    </div>

                </div>
                <div class="content">
                    <div>Resumen</div>
                </div>

                <div class="content">
                    <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($proyectoActual['resumen'] ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $proyectoActual['resumen'])); ?></span>
                </div>

        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    <?php endif; ?>
<?php endif; ?>
<?php
    $tituloAmbitos='';
    if(intval($year)>2016){
        switch ($motivoLetra) {
				case 'A':
				  $tituloAmbitos='INSTITUCION/LUGAR DONDE REALIZARA LA ACTIVIDAD';
				break;
				case 'B':
				  $tituloAmbitos='AMBITOS ACADEMICOS EN QUE REALIZARA LA ACTIVIDAD';
				break;
				case 'C':
				  $tituloAmbitos='INSTITUCION/LUGAR DE PROCEDENCIA DEL INVESTIGADOR INVITADO';
				break;

			}
    }
    else{
        $tituloAmbitos='AMBITOS ACADEMICOS EN QUE REALIZARA LA ACTIVIDAD';
    }
    ?>
<div class="content">
    <div><?php echo e($tituloAmbitos); ?></div>
    <table>
        <tr style="background-color: #999999;">
            <th>Institución</th><th>Ciudad</th><th>País</th><th>Desde</th><th>Hasta</th>
        </tr>
        <?php $__currentLoopData = $ambitos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ambito): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($ambito->institucion); ?></td><td><?php echo e($ambito->ciudad); ?></td><td><?php echo e($ambito->pais); ?></td><td><?php echo e(($ambito->desde)?date('d/m/Y', strtotime($ambito->desde)):''); ?></td><td><?php echo e(($ambito->hasta)?date('d/m/Y', strtotime($ambito->hasta)):''); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</div>
<?php if(intval($year)>2011): ?>
    <?php if(intval($year)==2012): ?>
        <div class="content">
            <div>OBJETIVOS DEL VIAJE - JUSTIFICACION Y RELACION DE LAS TAREAS A REALIZAR CON EL PROYECTO DE INVESTIGACION - RELEVANCIA INSTITUCIONAL</div>
        </div>
        <div class="content">
            <div>Si el motivo de la solicitud es A)Reuniones Científicas deberá aclarar si realiza otra actividad además de presentar su trabajo (por ej. coordinador/a, comentarista de ponencias, panelista, presentador/a de libros o alguna otra actividad)</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($objetivo ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $objetivo)); ?></span>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="content">
        <div>OBJETIVO DEL VIAJE Y RELACION DE LAS TAREAS A REALIZAR CON EL PROYECTO DE INVESTIGACION</div>
    </div>

    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($objetivo ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $objetivo)); ?></span>
    </div>
<?php endif; ?>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 300px;">MONTO SOLICITADO A LA UNLP (en pesos)</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($monto ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e('$' . number_format($monto, 2, ',', '.')); ?></span>

    </div>

</div>
<div class="content">
    <div>MONTO SOLICITADO A OTROS ORGANISMOS</div>
    <table>
        <tr style="background-color: #999999;">
            <th>Institución</th><th>Carácter</th><th>Importe</th>
        </tr>
        <?php $__currentLoopData = $montos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $monto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($monto->institucion); ?></td><td><?php echo e($monto->ciudad); ?></td><td><?php echo e('$' . number_format($monto->monto, 2, ',', '.')); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</div>
<div class="content">
    <div style="background-color: #000000; color: #ffffff"><?php echo e($motivo); ?></div>
</div>
<?php if($motivoLetra == 'A'): ?>
    <?php if(intval($year)>2012): ?>
        <?php if(intval($year)<2017): ?>
            <div class="content">
                <div>OBJETIVOS DEL VIAJE - JUSTIFICACION Y RELACION DE LAS TAREAS A REALIZAR CON EL PROYECTO DE INVESTIGACION - RELEVANCIA INSTITUCIONAL</div>
            </div>
            <div class="content">
                <div>Deberá aclarar si realiza otra actividad además de la actividad motivo de esta solicitada (por ej. coordinador/a, comentarista de ponencias, panelista, presentador/a de libros o alguna otra actividad)</div>
            </div>
        <?php else: ?>
            <div class="content">
                <div>OBJETIVOS DEL VIAJE - JUSTIFICACION Y RELACION CON EL PROYECTO DE INVESTIGACION</div>
            </div>
        <?php endif; ?>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($objetivo ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $objetivo)); ?></span>
        </div>
        <?php if(intval($year)>2016): ?>
            <div class="content">
                <div>RELEVANCIA INSTITUCIONAL: </div>
                    <ul>
                        <li>La importancia del evento con relación al tema del solicitante, -Su contribución al proyecto que integra</li>
                        <li>La relevancia de su participación para transferir y fortalecer líneas de investigación de la Unidad Académica</li>
                        <li>El establecimiento o afianzamiento de vínculos con otros equipos o investigadores particulares</li>
                        <li>Los potenciales canales de difusión que pueden surgir a partir del evento</li>
                    </ul>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $relevanciaA)); ?></span>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if($congreso): ?>
        <?php if(intval($year)<2023): ?>
            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    <?php
                        switch ($congreso) {
                        case 1:
                            $tipo_congreso = 'CONGRESO';
                            break;
                        case 2:
                            $tipo_congreso = 'CONFERENCIA';
                            break;
                    }
                        ?>
                    <span style="display: inline-block;width: 70px;">Tipo</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: <?php echo e($tipo_congreso ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($tipo_congreso); ?></span>

                </div>

            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if(intval($year)<2023): ?>
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                <?php
                    $labelCongreso = ($congreso==2)?'Título de la Conferencia':'Título del Trabajo';
                ?>
                <span style="display: inline-block;width: 140px;"><?php echo e($labelCongreso); ?></span> <span style="display: inline-block; border: 1px solid #ccc;width: 555px; padding-top: <?php echo e($titulotrabajo ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($titulotrabajo); ?></span>

            </div>

        </div>
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                <?php
                    $labelAutores = ($congreso==2)?'Autor':'Autores del Trabajo';
                ?>
                <span style="display: inline-block;width: 140px;"><?php echo e($labelAutores); ?></span> <span style="display: inline-block; border: 1px solid #ccc;width: 555px; padding-top: <?php echo e($autores ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($autores); ?></span>

            </div>

        </div>
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                <?php
                    $labelNombre = ($congreso==2)?'Congreso donde se dictará la conferencia':'Nombre del Congreso';
                ?>
                <span style="display: inline-block;width: 170px;"><?php echo e($labelNombre); ?></span> <span style="display: inline-block; border: 1px solid #ccc;width: 525px; padding-top: <?php echo e($congresonombre ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($congresonombre); ?></span>

            </div>

        </div>
        <?php if($nacional): ?>
            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    <?php
                        switch ($nacional) {
                        case 1:
                            $tipo_nacional = 'NACIONAL';
                            break;
                        case 2:
                            $tipo_nacional = 'INTERNACIONAL';
                            break;
                    }
                    ?>
                    <span style="display: inline-block;width: 70px;">Carácter</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: <?php echo e($tipo_nacional ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($tipo_nacional); ?></span>

                </div>

            </div>
        <?php endif; ?>
        <?php if(intval($year)>2016): ?>
            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    <span style="display: inline-block;width: 60px;">Lugar</span> <span style="display: inline-block; border: 1px solid #ccc;width: 255px; padding-top: <?php echo e($lugartrabajo ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($lugartrabajo); ?></span>
                    <span style="display: inline-block;width: 80px;">Fecha Inicio</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($trabajodesde ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($trabajodesde)?date('d/m/Y', strtotime($trabajodesde)):''); ?></span>
                    <span style="display: inline-block;width: 80px;">Fecha Fin</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($trabajohasta ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($trabajohasta)?date('d/m/Y', strtotime($trabajohasta)):''); ?></span>
                </div>
            </div>
        <?php else: ?>

            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    <span style="display: inline-block;width: 60px;">Lugar</span> <span style="display: inline-block; border: 1px solid #ccc;width: 405px; padding-top: <?php echo e($lugartrabajo ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($lugartrabajo); ?></span>
                    <span style="display: inline-block;width: 80px;">Fecha Inicio</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($trabajodesde ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($trabajodesde)?date('d/m/Y', strtotime($trabajodesde)):''); ?></span>

                </div>
            </div>
        <?php endif; ?>
        <?php if(intval($year)>2016): ?>
            <div class="content">
                <div>Relevancia del evento</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $relevancia)); ?></span>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                <span style="display: inline-block;width: 220px;">Nombre de la Reunión Científica</span> <span style="display: inline-block; border: 1px solid #ccc;width: 475px; padding-top: <?php echo e($congresonombre ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($congresonombre); ?></span>
            </div>
        </div>
        <?php if($nacional): ?>
            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    <?php
                        switch ($nacional) {
                        case 1:
                            $tipo_nacional = 'NACIONAL';
                            break;
                        case 2:
                            $tipo_nacional = 'INTERNACIONAL';
                            break;
                    }
                    ?>
                    <span style="display: inline-block;width: 70px;">Carácter</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: <?php echo e($tipo_nacional ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($tipo_nacional); ?></span>

                </div>

            </div>
            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    <span style="display: inline-block;width: 200px;">Link de la Reunión Científica</span> <span style="display: inline-block; border: 1px solid #ccc;width: 495px; padding-top: <?php echo e($link ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($link); ?></span>
                </div>
            </div>
        <?php endif; ?>
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                <span style="display: inline-block;width: 60px;">Lugar</span> <span style="display: inline-block; border: 1px solid #ccc;width: 255px; padding-top: <?php echo e($lugartrabajo ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($lugartrabajo); ?></span>
                <span style="display: inline-block;width: 80px;">Fecha Inicio</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($trabajodesde ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($trabajodesde)?date('d/m/Y', strtotime($trabajodesde)):''); ?></span>
                <span style="display: inline-block;width: 80px;">Fecha Fin</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($trabajohasta ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($trabajohasta)?date('d/m/Y', strtotime($trabajohasta)):''); ?></span>
            </div>
        </div>


        <div class="content">
            <div>Relevancia del evento</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $relevancia)); ?></span>
        </div>
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                <?php
                    $labelCongreso = ($congreso==2)?'Título de la Conferencia':'Título del Trabajo';
                ?>
                <span style="display: inline-block;width: 140px;"><?php echo e($labelCongreso); ?></span> <span style="display: inline-block; border: 1px solid #ccc;width: 555px; padding-top: <?php echo e($titulotrabajo ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($titulotrabajo); ?></span>

            </div>

        </div>
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                <?php
                    $labelAutores = ($congreso==2)?'Autor':'Autores del Trabajo';
                ?>
                <span style="display: inline-block;width: 140px;"><?php echo e($labelAutores); ?></span> <span style="display: inline-block; border: 1px solid #ccc;width: 555px; padding-top: <?php echo e($autores ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($autores); ?></span>

            </div>

        </div>



    <?php endif; ?>

    <?php
        $labelResumen = ($congreso==2)?' de la Conferencia':' del Trabajo';
    ?>

    <div class="content">
        <div>Resumen <?php echo e($labelResumen); ?></div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $resumen)); ?></span>
    </div>
    <?php if(intval($year)>2016): ?>
        <div class="content">
            <div class="content" style="margin-top: 10px;">

                <span style="display: inline-block;width: 200px;">Modalidad de la presentación</span> <span style="display: inline-block; border: 1px solid #ccc;width: 495px; padding-top: <?php echo e($modalidad ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($modalidad); ?></span>

            </div>

        </div>
    <?php endif; ?>
<?php endif; ?>
<?php if($motivoLetra == 'C'): ?>
    <div class="content">
        <div class="content" style="margin-top: 10px;">

            <span style="display: inline-block;width: 150px;">Profesor Visitante</span> <span style="display: inline-block; border: 1px solid #ccc;width: 445px; padding-top: <?php echo e($profesor ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($profesor); ?></span>

        </div>

    </div>
    <div class="content">
        <div class="content" style="margin-top: 10px;">

            <span style="display: inline-block;width: 200px;">Lugar de Origen del Prof. Visitante</span> <span style="display: inline-block; border: 1px solid #ccc;width: 495px; padding-top: <?php echo e($lugarprofesor ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($lugarprofesor); ?></span>

        </div>

    </div>
<?php endif; ?>
<?php if(intval($year)==2012): ?>
    <div class="content">
        <div style="font-weight: bold">Producción Científica, artística o desarrollo tecnológico en los últimos 5 años.</div>
    </div>
    <div class="content">
        <div>LIBROS (AUTORIAS)</div>
    </div>
    <div class="content">
        <div>Autor - Título - Editor - Edición(Nacional/Internacional) - ISBN - Lugar de Publicación - Año</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $libros)); ?></span>
    </div>
    <div class="content">
        <div>LIBROS (COMPILACIONES)</div>
    </div>
    <div class="content">
        <div>Compilador - Título - Editor - Edición(Nacional/Internacional) - ISBN - Lugar de Publicación - Año</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $compilados)); ?></span>
    </div>
    <div class="content">
        <div>CAPITULOS DE LIBROS</div>
    </div>
    <div class="content">
        <div>Autores - Capítulo/s - Título del Libro - Editor - Edición(Nacional/Internacional) - ISBN - Lugar de Publicación - Año</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $capitulos)); ?></span>
    </div>
    <?php
    $referato = (($tipo == 'Investigador Formado')&&($motivoLetra=='A'))?'con o sin referato':'solo con referato';
    $ponerReferato = (($tipo == 'Investigador En Formación')&&($motivoLetra=='A'))?'- Con Referato':'';
    ?>
    <div class="content">
        <div>ARTICULOS EN REVISTAS (<?php echo e($referato); ?>)</div>
    </div>
    <div class="content">
        <div>Autor/es - Título - Revista - ISSN - Volumen - Nro. - Páginas - Año <?php echo e($ponerReferato); ?> - Nacional o Internacional</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $articulos)); ?></span>
    </div>
    <div class="content">
        <div>CONGRESOS (TRABAJOS COMPLETOS PUBLICADOS EN ACTAS CON REFERATO)</div>
    </div>
    <div class="content">
        <div>Autor/es - Título trabajo - Congreso - Lugar - Volumen - Nro. - Páginas - Año - Fecha - Carácter(Nacional/Internacional)</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $congresos)); ?></span>
    </div>
    <div class="content">
        <div>PATENTES</div>
    </div>
    <div class="content">
        <div>Autor/es - Título - Código de Patente - Año</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $patentes)); ?></span>
    </div>
    <div class="content">
        <div>REGISTROS DE PROPIEDAD INTELECTUAL</div>
    </div>
    <div class="content">
        <div>Tipo - Título - Titular/es - Registro Nro. - País - Autor/es</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $intelectuales)); ?></span>
    </div>
    <div class="content">
        <div>INFORMES TECNICOS</div>
    </div>
    <div class="content">
        <div>Autor/es - Título - Año - Institución</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $informes)); ?></span>
    </div>
    <div class="content">
        <div style="font-weight: bold">Formación de Recursos Humanos realizada durante toda su carrera como docente-investigador.</div>
    </div>
    <div class="content">
        <div>DIR./CODIR. TESIS DE POSGRADO APROBADAS</div>
    </div>
    <div class="content">
        <div>Año - Apellido y Nombre - Tema - Universidad - Calificación - (Dir./Codir.) - (Doctorado/Maestría)</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $tesis)); ?></span>
    </div>
    <div class="content">
        <div>DIR./CODIR. BECAS DE POSGRADO / DIR./CODIR. TESIS DE POSGRADO EN REALIZACION</div>
    </div>
    <div class="content">
        <div>Año - Apellido y Nombre - Tema - Universidad - (Dir./Codir.) - Si es Tesis (Doctorado/Maestría) - Si es Beca (Tipo de Beca)</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $becas)); ?></span>
    </div>
    <div class="content">
        <div>DIR./CODIR. TESINAS DE GRADO / DIR./CODIR. BECAS DE ENTRENAMIENTO</div>
    </div>
    <div class="content">
        <div>Año - Apellido y Nombre - Tema - Universidad - (Dir./Codir.) - (Tesina de Grado/Beca de Entrenamiento)</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $tesinas)); ?></span>
    </div>
<?php endif; ?>
<?php if(intval($year)>2012): ?>
    <?php if($motivoLetra=='B'): ?>
        <div class="content">
            <div style="font-weight: bold">PLAN DE TRABAJO DE INVESTIGACIÓN (para los tipo B)</div>
        </div>
        <div class="content">
            <div>1. Objetivo general de la estadía</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $generalB)); ?></span>
        </div>
        <div class="content">
            <div>2. Objetivos específicos de la estadía</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $especificoB)); ?></span>
        </div>
        <?php if(intval($year)>2016): ?>
            <div class="content">
                <div>3. Plan de trabajo de investigación a realizar en el período</div>
            </div>
        <?php else: ?>
            <div class="content">
                <div>3. Detalle de las actividades de invest. a realizar en el período relacionado con el proy. de invest. en el que participa</div>
            </div>
        <?php endif; ?>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $actividadesB)); ?></span>
        </div>
        <div class="content">
            <div>4.- Cronograma</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $cronogramaB)); ?></span>
        </div>
        <?php if(intval($year)>2017): ?>
            <div class="content">
                <div>5.- Justificación de la realización de la estadía y relación con el proyecto de investigación en el que participa</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $justificacionB)); ?></span>
            </div>
        <?php endif; ?>
        <?php if(intval($year)>2016): ?>
            <?php if(intval($year)>2017): ?>
                <div class="content">
                    <div>6. Relevancia institucional</div>
                    <ul>
                        <li>La afinidad y los aportes del grupo receptor a la línea de investigación del solicitante</li>
                        <li>La correspondencia del plan de trabajo a realizar con la línea de investigación del solicitante así como su factibilidad</li>
                        <li>Los aportes del desarrollo del plan de trabajo a la línea de investigación del solicitante</li>
                        <li>La transferencia que realizará el solicitante a su equipo de investigación, Unidad de Investigación y/o Unidad Académica a partir de la realización de su estadía</li>
                    </ul>
                </div>
            <?php else: ?>
                <div class="content">
                    <div>5.- Relevancia institucional: (Detalle de las actividades de investigación a realizar relacionadas con el proyecto de investigación en el que participa, la afinidad y los aportes del grupo receptor a la línea de investigación del solicitante, la transferencia que realizará el solicitante a su equipo de investigación, a la unidad de investigación y a la unidad académica</div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="content">
                <div>5.- Aportes al grupo de investigación</div>
            </div>
        <?php endif; ?>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $aportesB)); ?></span>
        </div>
        <?php if(intval($year)>2016): ?>
            <?php if(intval($year)>2017): ?>
                <div class="content">
                    <div>7. Relevancia del lugar donde realiza la estadía. Justifique la elección del lugar</div>
                </div>
            <?php else: ?>
                <div class="content">
                    <div>6. Relevancia del lugar donde realiza la estadía. Justifique la elección del lugar</div>
                </div>
            <?php endif; ?>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $relevanciaB)); ?></span>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <?php if($motivoLetra=='C'): ?>
        <div class="content">
            <div style="font-weight: bold">PLAN DE TRABAJO DE INVESTIGACIÓN (para los tipo C)</div>
        </div>
        <div class="content">
            <div>1. Objetivo general de la estadía</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $objetivosC)); ?></span>
        </div>
        <?php if(intval($year)>2016): ?>
            <div class="content">
                <div>2. Plan de trabajo de investigación a realizar en el período</div>
            </div>
        <?php else: ?>
            <div class="content">
                <div>2. Plan de actividades de invest. a realizar en el período, en relación con el proy. de investigación del grupo receptor</div>
            </div>
        <?php endif; ?>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $planC)); ?></span>
        </div>
        <?php if(intval($year)>2016): ?>
            <div class="content">
                <div>3. Relación del plan de trabajo del investigador invitado con el proyecto de investigación acreditado del grupo receptor</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $relacionProyectoC)); ?></span>
            </div>
        <?php else: ?>
            <div class="content">
                <div>3. Aportes del desarrollo del plan de trabajo al grupo de investigación, Unidad de Investigación y/o Unidad Académica</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $aportesC)); ?></span>
            </div>
        <?php endif; ?>
        <?php if(intval($year)>2016): ?>
            <div class="content">
                <div>4. Aportes del desarrollo del plan de trabajo al grupo de investigación, Unidad de Investigación y/o Unidad Académica</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $aportesC)); ?></span>
            </div>
        <?php else: ?>
            <div class="content">
                <div>4. Otras actividades (ejemplo: dictado de cursos, seminarios, participación en eventos científicos, etc.</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $actividadesC)); ?></span>
            </div>
        <?php endif; ?>
        <?php if(intval($year)>2016): ?>
            <div class="content">
                <div>5. Otras actividades</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: <?php echo e($relevanciaA ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $actividadesC)); ?></span>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif; ?>



<div class="content">

    <div>Declaro bajo juramento que los datos consignados son veraces. Asimismo me comprometo, a presentar un
        informe por escrito, constancia de la rendición del área administrativo-financiera efectuada en mi Unidad
        Académica y adjuntar para los Subsidios tipo A la constancia de participación y para los Subsidios tipo B
        una constancia del trabajo realizado, avalada por la institución receptora, en el término de 60 días después
        de finalizada la actividad.</div>
</div>

<div class="signature">
    <div class="signature-line">Lugar y Fecha</div>
    <div class="signature-line">Firma y Aclaración</div>
</div>

<div class="content">
    <div style="text-align: center;">AVAL DE LA <?php echo e($facultadplanilla); ?></div>
</div>
<div class="signature">
    <div class="signature-line">Lugar y Fecha </div>
    <div class="signature-line">Firma del Decano</div>
</div>


<div class="page-break"></div> <!-- Esto genera el salto de página -->
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Apellido y Nombres</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: <?php echo e($solicitante ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($solicitante); ?></span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Facultad</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: <?php echo e($facultadplanilla ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($facultadplanilla); ?></span>

    </div>

</div>



<div class="content">
    <div><strong>Indicar y describir la aplicación del subsidio en caso que le sea otorgado. La descripcion deberá ser lo mas
            detallada y precisa posible.</strong></div>
</div>
<div class="content">
    <div style="text-align: center"><strong>PRESUPUESTO ESTIMADO PRELIMINAR</strong></div>
</div>
<div class="content">
    <div><?php echo e($tituloAmbitos); ?></div>
    <table>
        <tr style="background-color: #999999;">
            <th>Institución</th><th>Ciudad</th><th>País</th><th>Desde</th><th>Hasta</th>
        </tr>
        <?php $__currentLoopData = $ambitos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ambito): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e($ambito->institucion); ?></td><td><?php echo e($ambito->ciudad); ?></td><td><?php echo e($ambito->pais); ?></td><td><?php echo e(($ambito->desde)?date('d/m/Y', strtotime($ambito->desde)):''); ?></td><td><?php echo e(($ambito->hasta)?date('d/m/Y', strtotime($ambito->hasta)):''); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </table>
</div>
<?php
    $total = 0;
?>
<?php $__currentLoopData = $tipoPresupuestos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tipoPresupuesto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

    <div class="content">
        <div><?php echo e($tipoPresupuesto->nombre); ?></div>

        <table>
            <tr style="background-color: #999999;">
                <th>FECHA</th><th>DESCRIPCION / CONCEPTO</th><th>Importe</th>
            </tr>
            <?php
                $subtotal = 0;
            ?>
            <?php $__currentLoopData = $presupuestos->where('tipo_presupuesto_id', $tipoPresupuesto->id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $presupuesto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e(($presupuesto->fecha) ? date('d/m/Y', strtotime($presupuesto->fecha)) : ''); ?></td>
                    <td>
                        <?php if($tipoPresupuesto->id == 2): ?>
                            <?php
                            $detalles = explode('|', $presupuesto->detalle);
                            $concepto = $detalles[0];
                            ?>
                            <?php if($concepto === 'Viaticos'): ?>

                                Viáticos - Días: <?php echo e($detalles[1]); ?> - Lugar: <?php echo e($detalles[2]); ?>

                            <?php elseif($concepto === 'Pasajes'): ?>

                                Pasajes - <?php echo e($detalles[1]); ?> - Destino: <?php echo e($detalles[2]); ?>

                            <?php elseif($concepto === 'Alojamiento'): ?>

                                Alojamiento - Noches: <?php echo e($detalles[1]); ?> - Lugar: <?php echo e($detalles[2]); ?>

                            <?php elseif($concepto === 'Inscripcion'): ?>

                                Inscripción - Descripción: <?php echo e($detalles[1]); ?>

                            <?php elseif($concepto === 'Otros'): ?>
                                Otros - Descripción: <?php echo e($detalles[1]); ?>

                            <?php endif; ?>
                        <?php else: ?>

                        <?php echo e($presupuesto->detalle); ?>

                        <?php endif; ?>
                    </td>
                    <td style="text-align: right;"><?php echo e('$' . number_format($presupuesto->monto, 2, ',', '.')); ?></td>
                </tr>
                <?php
                    $subtotal += $presupuesto->monto;
                    $total += $presupuesto->monto;
                ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <!--<tr style="background-color: #e1e1e1;">
                <td colspan="3" style="text-align: right;">SUBTOTAL: <?php echo e('$' . number_format($subtotal, 2, ',', '.')); ?></td>
            </tr>-->
        </table>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<table>
    <tr style="background-color: #e1e1e1;">
        <td colspan="3" style="text-align: right;">TOTAL: <?php echo e('$' . number_format($total, 2, ',', '.')); ?></td>
    </tr>
</table>





<div class="signature">
    <div class="signature-line">Lugar y Fecha</div>
    <div class="signature-line">Firma y Aclaración</div>
</div>



</body>
</html>
<?php /**PATH /var/www/sicadi/resources/views/viajes/pdfsolicitud.blade.php ENDPATH**/ ?>