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
<?php endif; ?>
<div class="header">
    <div class="logo">

        <img src="<?php echo e(public_path('/images/unlp.png')); ?>" alt="UNLP Logo" class="logo">

    </div>
    <div class="text-content">
        <div class="title">
            SOLICITUD DE SUBSIDIOS <?php echo e($year); ?>

        </div>
        <div class="subtitle">
            Jóvenes Investigadores de la UNLP
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
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Título de grado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 350px; padding-top: <?php echo e($titulo ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($titulo); ?></span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 70px;">F. Egreso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($egreso ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($egreso)?date('d/m/Y', strtotime($egreso)):''); ?></span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Título de posgrado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 350px; padding-top: <?php echo e($tituloposgrado ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($tituloposgrado); ?></span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 70px;">F. Egreso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: <?php echo e($egresoposgrado ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($egresoposgrado)?date('d/m/Y', strtotime($egresoposgrado)):''); ?></span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 230px;">Lugar de Trabajo de Inv. en la UNLP</span> <span style="display: inline-block; border: 1px solid #ccc;width: 465px; padding-top: <?php echo e($unidad ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($unidad); ?></span>

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
<?php if(!empty($beca)): ?>
<div class="content">
    <div>BECARIO</div>
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 70px;">Institución</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: <?php echo e($beca->institucion ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($beca->institucion); ?></span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100px;">Nivel de beca</span> <span style="display: inline-block; border: 1px solid #ccc;width: 230px; padding-top: <?php echo e($beca->beca ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($beca->beca); ?></span><span style="display: inline-block;width: 105px;"></span>
        <span style="display: inline-block;width: 50px;">Período</span> <span style="display: inline-block; border: 1px solid #ccc;width: 200px; padding-top: <?php echo e($beca->desde ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e(($beca->desde)?date('d/m/Y', strtotime($beca->desde)):''); ?> - <?php echo e(($beca->hasta)?date('d/m/Y', strtotime($beca->hasta)):''); ?></span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Lugar de Trabajo</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: <?php echo e($unidadbeca ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($unidadbeca); ?></span>

    </div>

</div>
<?php endif; ?>
<?php if(!empty($becas)): ?>
    <div class="content">
        <div>BECAS ANTERIORES</div>

        <table>
            <tr style="background-color: #999999;">
                <th>Nivel beca</th><th>UNLP</th><th>F. desde</th><th>F. hasta</th>
            </tr>
            <?php $__currentLoopData = $becas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $becaAnt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr <?php if($becaAnt->agregada): ?>
                        style="background-color: #CCCCCC;"
                    <?php endif; ?>>
                    <td><?php echo e($becaAnt->beca); ?> - <?php echo e($becaAnt->institucion); ?></td><td><?php echo e(($becaAnt->unlp)?'SI':'NO'); ?></td><td><?php echo e(($becaAnt->desde)?date('d/m/Y', strtotime($becaAnt->desde)):''); ?></td><td><?php echo e(($becaAnt->hasta)?date('d/m/Y', strtotime($becaAnt->hasta)):''); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    </div>

<?php endif; ?>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 350px;">Es o ha sido DIR./CODIR. de proyectos de acreditación</span> <span style="display: inline-block; border: 1px solid #ccc;width: 345px; padding-top: <?php echo e($director ? '0' : '15px'); ?>; background-color: #e1e1e1;"><?php echo e($director); ?></span>

    </div>

</div>
<?php if(!empty($proyectosActuales)): ?>
    <div class="content">
        <div>PROYECTO/S ACREDITADO/S EN EL/LOS QUE PARTICIPA ACTUALMENTE</div>

        <table>
            <tr style="background-color: #999999;">
                <th>Código</th><th>Título</th><th>Director</th><th>Inicio</th><th>Fin</th>
            </tr>
            <?php $__currentLoopData = $proyectosActuales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proyectoActual): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($proyectoActual->codigo); ?></td><td><?php echo e($proyectoActual->titulo); ?></td><td><?php echo e($proyectoActual->director); ?></td><td><?php echo e(($proyectoActual->desde)?date('d/m/Y', strtotime($proyectoActual->desde)):''); ?></td><td><?php echo e(($proyectoActual->hasta)?date('d/m/Y', strtotime($proyectoActual->hasta)):''); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    </div>

<?php endif; ?>
<?php if(!empty($proyectosAnteriores)): ?>
    <div class="content">
        <div>PROYECTO/S ACREDITADO/S EN EL/LOS QUE PARTICIPO ANTERIORMENTE</div>

        <table>
            <tr style="background-color: #999999;">
                <th>Código</th><th>Título</th><th>Director</th><th>Inicio</th><th>Fin</th>
            </tr>
            <?php $__currentLoopData = $proyectosAnteriores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $proyectoAnterior): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr
                <?php if($proyectoAnterior->agregado): ?>
                    style="background-color: #e1e1e1;"
                <?php endif; ?>
                >
                    <td><?php echo e($proyectoAnterior->codigo); ?></td><td><?php echo e($proyectoAnterior->titulo); ?></td><td><?php echo e($proyectoAnterior->director); ?></td><td><?php echo e(($proyectoAnterior->desde)?date('d/m/Y', strtotime($proyectoAnterior->desde)):''); ?></td><td><?php echo e(($proyectoAnterior->hasta)?date('d/m/Y', strtotime($proyectoAnterior->hasta)):''); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </table>
    </div>

<?php endif; ?>
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
<?php if(intval($year)>2012): ?>
<div class="content">
    <div>Breve descripción de las actividades de I/D que plantea en el marco del proyecto en que se desempeña el solicitante</div>
</div>
<div class="content">
        <span style="display: inline-block; border: 1px solid #ccc; width: 695px; padding-top: <?php echo e($objetivo ? '0' : '15px'); ?>; background-color: #e1e1e1;  white-space: pre-wrap;">
            <?php echo nl2br(e($objetivo)); ?>

        </span>
</div>
<?php if(intval($year)>2016): ?>
<div class="content">
    <div>Justificar el pedido de fondos detallado en el presupuesto preliminar. Además, para cada ítem que solicita en el
        presupuesto preliminar deberá a) detallar el mismo y b) justificar su pedido. En el caso de solicitar bibliografía deberá
        indicar título, autor, editorial, etc.</div>
</div>
<?php else: ?>
    <div class="content">
        <div>Justificar el pedido de fondos detallado en el presupuesto preliminar</div>
    </div>
<?php endif; ?>
<div class="content">
    <span style="display: inline-block; border: 1px solid #ccc;width: 695px; padding-top: <?php echo e($justificacion ? '0' : '15px'); ?>; background-color: #e1e1e1; white-space: pre-wrap;"><?php echo nl2br(e( $justificacion)); ?></span>
</div>
<?php else: ?>
    <div class="content">
        <div>Breve descripción de las actividades de I/D que plantea en el marco del proyecto en que se desempeña el solicitante</div>
    </div>
    <div class="content">
    <span style="display: inline-block; border: 1px solid #ccc; width: 695px; padding-top: <?php echo e($objetivo ? '0' : '15px'); ?>; background-color: #e1e1e1;  white-space: pre-wrap;">
        <?php echo nl2br(e($objetivo)); ?>

    </span>

    </div>
    </div>
<?php endif; ?>
<div class="content">
    <div>Declaración Jurada <strong>(Sólo en caso de haber sido adjudicatario de subsidios anteriores)</strong></div>
</div>
<div class="content">
    <?php
        $yearAnt = intval($year)-2;
    ?>
    <div>Declaro que al momento de la presentación de la solicitud de subsidios <?php echo e($year); ?>, he entregado en la Secretaría
        de Ciencia y Técnica de la Universidad Nacional de La Plata el informe y constancia de la rendición
        efectuada en mi Unidad Académica correspondiente al subsidio OTORGADO EN EL PERIODO <?php echo e($year); ?> al
        <?php echo e($yearAnt); ?> inclusive. Tomo conocimiento que el no cumplimiento de lo mencionado precedentemente es motivo de
        exclusión de esta presentación.</div>
</div>

<div class="signature">

    <div class="signature-line">Firma</div>
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
            <tr style="background-color: #e1e1e1;">
                <td colspan="3" style="text-align: right;">SUBTOTAL: <?php echo e('$' . number_format($subtotal, 2, ',', '.')); ?></td>
            </tr>
        </table>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<table>
    <tr style="background-color: #e1e1e1;">
        <td colspan="3" style="text-align: right;">TOTAL: <?php echo e('$' . number_format($total, 2, ',', '.')); ?></td>
    </tr>
</table>




<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100%;">La presente tiene carácter de declaración jurada.</span>
    </div>
</div>
<div class="signature">
    <div class="signature-line">Lugar y Fecha</div>
    <div class="signature-line">Firma y Aclaración</div>
</div>
<div class="signature">
    <div class="signature-line">Aval del Dir. de Proy. Acred. o Beca </div>
    <div class="signature-line">AVAL DEL DECANO</div>
</div>


</body>
</html>
<?php /**PATH /var/www/sicadi/resources/views/jovens/pdfsolicitud.blade.php ENDPATH**/ ?>