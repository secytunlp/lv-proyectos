<!DOCTYPE html>
<html>
<head>
    <title>Evaluación</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            margin-top: 150px; /* Asegúrate de dejar espacio para el header */
            padding: 0;
        }
        .header {

            width: 100%;
            /*border: 1px solid #000;*/ /* Borde alrededor del encabezado */
            padding: 10px; /* Espacio dentro del borde */
            margin-bottom: 10px;

            position: fixed;
            top: 0px; /* Asegúrate de que se mantenga en la parte superior de la página */
            left: 0px;
            right: 0px;
        }
        .headerSub {
            display: table; /* Utiliza display: table para alinear la imagen y el texto */
            width: 100%;
            border: 1px solid #000;
            padding: 10px; /* Espacio dentro del borde */
            margin-bottom: 10px;
            box-sizing: border-box; /* Asegura que el padding y el borde estén incluidos en el ancho total */

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
        .signature-aclaracion { margin-top: 100px; text-align: center; margin-bottom: 0px; }
        .signature { margin-top: 0px; text-align: center; }
        .signature div { display: inline-block; width: 40%; text-align: center; }
        .signature-aclaracion div { display: inline-block; width: 40%; text-align: center; }
        .signature-line {
            border-top: 1px solid black;
            padding-top: 5px;
            margin-top: 20px;
        }
        .signature-no-line {

            /*padding-top: 5px;
            margin-top: 20px;*/
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

        }
        th{
            text-align: center;
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
<?php if ($estado === 'En evaluación'): ?>
    <div class="watermark">VISTA_PRELIMINAR</div>
<?php endif; ?>
<div class="header">

    <div class="headerSub">
        <div class="logo">

            <img src="<?php echo e(public_path('/images/unlp.png')); ?>" alt="UNLP Logo" class="logo">

        </div>
        <div class="text-content">
            <div class="title">
                SOLICITUD DE SUBSIDIOS <?php echo e($year); ?>

            </div>
            <div class="subtitle">
                Jóvenes Investigadores de la UNLP<br>
                <strong>PLANILLA DE EVALUACION <?php echo e($year); ?></strong>
            </div>
        </div>
        <div style="clear: both;"></div>

    </div>
    <div class="content">

            <span style="display: inline-block;width: 125px;vertical-align: middle;">Apellido y Nombres</span> <span style="display: inline-block; width: 205px; padding-top: <?php echo e($solicitante ? '0' : '15px'); ?>;vertical-align: middle;"><?php echo e($solicitante); ?></span>
            <span style="display: inline-block;width: 90px;vertical-align: middle;">U. Académica</span> <span style="display: inline-block; width: 265px; padding-top: <?php echo e($facultadplanilla ? '0' : '15px'); ?>;vertical-align: middle; "><?php echo e($facultadplanilla); ?></span>


    </div>
    <div style="clear: both;"></div>
</div>
<div class="content">
    <table>
<tr>
    <tr style="background-color: #000;color:#FFF">
        <th>P. Max/ITEM</th><th colspan="3">DETALLE Y PUNTAJE</th><th>P. OTORGADO</th>
    </tr>
    <tr style="background-color: #e1e1e1;">
        <td style="text-align: center;border-bottom: none;">A</td><td colspan="4" style="text-align: center;border-bottom: none;border-right: none;">ANTECEDENTES ACADÉMICOS DEL SOLICITANTE</td>
    </tr>
    <?php
       $primerPosgrado = $posgradoMaximos->first();
       $puntajePosgrado = $evaluacion->puntaje_posgrados->first();
        //dd($puntajePosgrado);
        $puntajePosgradoSeleccionado = 0;
        foreach ($posgradoMaximos as $posgradoMaximo){
            if(($puntajePosgrado) && ($puntajePosgrado->joven_evaluacion_planilla_posgrado_max_id == $posgradoMaximo->id)){
                $puntajePosgradoSeleccionado = $posgradoMaximo->maximo;
            }

        }
        $total = 0;
    ?>
    <tr style="background-color: #e1e1e1;">
        <td style="text-align: center;border-top: none;"></td>
        <td style="border-top: none;" colspan="4">Título de posgrado: hasta <?php echo e($maxPosgrado); ?> puntos más por el título de posgrado obtenido en la especialidad (si lo hubiere)</td>
    </tr>

    <?php for($i = 0; $i < count($posgradoMaximos); $i++): ?>
        <tr>
            <?php if($i == 0): ?>
                <td style="text-align: center;border-bottom: none;background-color: #e1e1e1;"><?php echo e($primerPosgrado->nombre); ?> <?php echo e($i + 1); ?></td>
            <?php else: ?>
                <td style="text-align: center;border-top: none;background-color: #e1e1e1;"><strong>Max. <?php echo e($maxPosgrado); ?> pt.</strong></td>
            <?php endif; ?>

            <td colspan="3" style="text-align: left;border: none;border-right: none;">
                <?php for($j = 0; $j < 2; $j++): ?>
                    <?php
                        // Obtener el siguiente elemento
                        $siguientePosgradoMaximo = isset($posgradoMaximos[$i + $j]) ? $posgradoMaximos[$i + $j] : null;
                    ?>

                    <?php if($siguientePosgradoMaximo): ?>
                        <?php

                            $imgSINO = (($puntajePosgrado) && ($puntajePosgrado->joven_evaluacion_planilla_posgrado_max_id == $siguientePosgradoMaximo->id)) ? 'si' : 'no';

                            // Define the image path based on the $imgSINO value
                            $imgSrc = public_path('/images/' . $imgSINO . '.jpg');
                        ?>
                        <div style="width: 50%; float: left; text-align: left; border-right: none;">
                            <img src="<?php echo e($imgSrc); ?>" alt="<?php echo e($imgSINO == 'no' ? 'No' : 'Sí'); ?>">
                            <?php echo e($siguientePosgradoMaximo->posgrado_nombre); ?> (<?php echo e($siguientePosgradoMaximo->maximo); ?> pt.)
                        </div>
                    <?php endif; ?>
                <?php endfor; ?>
            </td>

            <?php if($i == 0): ?>
                <td style="text-align: center;border-bottom: none;background-color: #e1e1e1;"> <strong><?php echo e(number_format($puntajePosgradoSeleccionado, 2)); ?></strong></td>
            <?php else: ?>
                <td style="text-align: center;border-top: none;background-color: #e1e1e1;"></td>
            <?php endif; ?>
        </tr>
        <?php
            // Incrementar el índice para evitar procesar el mismo elemento
            $i++; // Aumentar aquí para que el bucle for avance más rápido
        ?>
    <?php endfor; ?>
    <?php
        //$total += $puntajePosgradoSeleccionado;
        $iterarAntAcad=0;
        $totalGrupo=0;
        $totalItem=0;
    ?>
    <?php $__currentLoopData = $antAcadMaximos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $antAcadMaximo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $tope = (($antAcadMaximo->maximo!=0)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?((($antAcadMaximo->minimo==$antAcadMaximo->tope)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?'':'<strong>Max. '.$antAcadMaximo->tope.'pt.</strong>'):'<strong>Max. '.$antAcadMaximo->tope.'pt.</strong>';
            $hasta = (($antAcadMaximo->maximo!=0)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?((($antAcadMaximo->minimo==$antAcadMaximo->tope)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?$antAcadMaximo->maximo. ' pt.':$antAcadMaximo->maximo. ' c/u'):'Hasta '.$antAcadMaximo->tope;

            $puntajeAntAcad = $evaluacion->puntaje_ant_acads->where('joven_evaluacion_planilla_ant_acad_max_id', $antAcadMaximo->id)->first();

            $puntaje=($puntajeAntAcad)?(int) $puntajeAntAcad->puntaje:'';


        ?>
        <tr>
        <?php if(($antAcadMaximo->minimo==0)&&($antAcadMaximo->tope==0)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo)): ?>
            <?php
                $checkedposgrado = ($puntaje==2)?'SI':'NO';
                $factor = ($puntaje==2)?'1':$puntaje;
                 $puntaje=($puntaje==2)?1:$puntaje;
                 $totalItem=($totalGrupo+$puntajePosgradoSeleccionado)*$factor;
                 $totalGrupo=0;
            ?>

                <td style="text-align: center;background-color: #e1e1e1; border-bottom: none"><?php echo e($antAcadMaximo->grupo_nombre); ?><?php echo e($iterarAntAcad+2); ?><br></td>
                <td colspan="4" style="background-color: #e1e1e1;text-align: left;">
                    Se aplicará un "factor de eficiencia" F que multiplicará
                    al resultado de la suma de los puntajes correspondientes a A.1), A.2) y A.3): Llamando G al número de años transcurridos
                    desde la obtención del título de grado. <br>Si ya obtuvo el grado académico superior de la especialidad, o G&lt;6 entonces
                    F=1 <br>Si aún no obtuvo el grado académico superior de la especialidad y G&gt;=6 entonces: SI 6=&lt; G &lt;7 entonces F=0.9 -- SI
                    7=&lt; G &lt;8 entonces F=0.8 -- SI 8=&lt; G &lt;9 entonces F=0.7 -- SI 9=&lt; G &lt;10 entonces F=0.6 -- SI G&gt;=10 entonces F=0.5</td>
            </tr>
            <tr>
                <td style="text-align: center;background-color: #e1e1e1; border-tope: none"></td>
                <td style="text-align: left;"><?php echo e($antAcadMaximo->ant_acad_nombre); ?></td>
                <td style="text-align: right;">

                    PG. Sup.: <?php echo e($checkedposgrado); ?>


                </td>
                <td style="text-align: right;">
                    F: <?php echo e($factor); ?></span></td>
                <td style="text-align: center;">
                    <strong><?php echo e(number_format($totalItem, 2)); ?></strong>
                </td>

        <?php else: ?>
            <td style="text-align: center;background-color: #e1e1e1;"><?php echo e($antAcadMaximo->grupo_nombre); ?><?php echo e($iterarAntAcad+2); ?><br><?php echo $tope; ?></td>


                <?php

                    $control = (($antAcadMaximo->maximo!=0)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?((($antAcadMaximo->minimo==$antAcadMaximo->tope)&&($antAcadMaximo->minimo==$antAcadMaximo->maximo))?1:2):3; //1=check 2=c/u 3=hasta
                    switch ( $control) {
                        case '1' :
                            $totalItem = ($puntajeAntAcad)?$puntaje:0;

                        break;
                        case '2' :
                            $totalItem = ($puntajeAntAcad)?$puntaje*$antAcadMaximo->maximo:0;
                            if($antAcadMaximo->tope && $totalItem>$antAcadMaximo->tope){
                                $totalItem = $antAcadMaximo->tope;
                            }
                        break;
                        case '3' :
                            $totalItem = ($puntajeAntAcad)?$puntaje:0;
                            if($antAcadMaximo->tope && $totalItem>$antAcadMaximo->tope){
                                $totalItem = $antAcadMaximo->tope;
                            }
                        break;

                    }


                    $descripcion = str_replace('#puntaje#', '<strong>'.$antAcadMaximo->maximo.' puntos</strong>',$antAcadMaximo->ant_acad_nombre);
                ?>
                <td style="text-align: left;width: 55%"><?php echo $descripcion; ?></td>
                <td style="text-align: right">
                    <?php echo $hasta; ?>

                </td>
                <td style="text-align: right;">
                    <?php echo e($puntaje); ?>

                </td>
                <td style="text-align: center;">
                    <strong> <?php echo e(number_format($totalItem, 2)); ?></strong>
                </td>

        <?php endif; ?>
        </tr>
        <?php

            $totalGrupo += $totalItem;
            //echo $antAcadMaximo->ant_acad_nombre.' puntaje: '.$totalItem.' total: '.$totalGrupo.'<br>';
            $iterarAntAcad ++;
        ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php
            $ultimoAntAcadMaximo = $antAcadMaximos->last();
            //dd($ultimoAntAcadMaximo);
        ?>
        <?php if($ultimoAntAcadMaximo->grupo_maximo!=0): ?>
            <?php
                $totalGrupo = ($totalGrupo>$ultimoAntAcadMaximo->grupo_maximo)?$ultimoAntAcadMaximo->grupo_maximo:$totalGrupo;
            ?>
            <tr>
                <td colspan="4" style="text-align: right;background-color: #e1e1e1;"> Subtotal <?php echo e($ultimoAntAcadMaximo->grupo_nombre); ?> (max. <?php echo e($ultimoAntAcadMaximo->grupo_maximo); ?>)</td><td style="text-align: center;background-color: #e1e1e1;"><strong><?php echo e(number_format($totalGrupo, 2)); ?></strong></td>
            </tr>
        <?php endif; ?>
        <?php
            $total += $totalGrupo;

        ?>
        <tr style="background-color: #e1e1e1;">
            <td style="text-align: center;">B</td><td colspan="4" style="text-align: center;">ANTECEDENTES DOCENTES</td>
        </tr>
        <?php
            $primerCargo = $cargoMaximos->first();
            $puntajeCargo = $evaluacion->puntaje_cargos->first();
             //dd($puntajeCargo);
             $puntajeCargoSeleccionado = 0;
             foreach ($cargoMaximos as $cargoMaximo){
                 if(($puntajeCargo) && ($puntajeCargo->joven_evaluacion_planilla_cargo_max_id == $cargoMaximo->id)){
                    $puntajeCargoSeleccionado = $cargoMaximo->maximo;
                }
             }


        ?>


        <?php for($i = 0; $i < count($cargoMaximos); $i++): ?>
            <tr>
                <?php switch($i):
                    case (0): ?>
                        <td style="text-align: center;border-bottom: none;background-color: #e1e1e1;height: 10px;"></td>
                        <?php break; ?>
                    <?php case (2): ?>
                        <td style="text-align: center;border-bottom: none;border-top: none;background-color: #e1e1e1;">B1</td>
                        <?php break; ?>
                    <?php case (4): ?>
                        <td style="text-align: center;border-bottom: none;border-top: none;background-color: #e1e1e1;"><strong>Max. <?php echo e($maxCargo); ?> pt.</strong></td>
                        <?php break; ?>
                    <?php case (6): ?>
                        <td style="text-align: center;border-bottom: none;border-top: none;background-color: #e1e1e1;height: 10px;"></td>
                        <?php break; ?>
                    <?php case (8): ?>
                        <td style="text-align: center;border-top: none;background-color: #e1e1e1;height: 10px;"></td>
                        <?php break; ?>

                <?php endswitch; ?>


                <td colspan="3" style="text-align: left;border: none;border-right: none;">
                    <?php for($j = 0; $j < 2; $j++): ?>
                        <?php
                            // Obtener el siguiente elemento
                            $siguienteCargoMaximo = isset($cargoMaximos[$i + $j]) ? $cargoMaximos[$i + $j] : null;
                        ?>

                        <?php if($siguienteCargoMaximo): ?>
                            <?php

                                $imgSINO = (($puntajeCargo) && ($puntajeCargo->joven_evaluacion_planilla_cargo_max_id == $siguienteCargoMaximo->id)) ? 'si' : 'no';

                                // Define the image path based on the $imgSINO value
                                $imgSrc = public_path('/images/' . $imgSINO . '.jpg');
                            ?>
                            <div style="width: 50%; float: left; text-align: left; border-right: none;">
                                <img src="<?php echo e($imgSrc); ?>" alt="<?php echo e($imgSINO == 'no' ? 'No' : 'Sí'); ?>">
                                <?php echo e($siguienteCargoMaximo->cargo_nombre); ?> (<?php echo e($siguienteCargoMaximo->maximo); ?> pt.)
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </td>

                    <?php switch($i):
                        case (0): ?>
                            <td style="text-align: center;border-bottom: none;background-color: #e1e1e1;"></td>
                            <?php break; ?>
                        <?php case (2): ?>
                            <td style="text-align: center;border-bottom: none;border-top: none;background-color: #e1e1e1;"></td>
                            <?php break; ?>
                        <?php case (4): ?>
                            <td style="text-align: center;border-bottom: none;border-top: none;background-color: #e1e1e1;"><strong><?php echo e(number_format($puntajeCargoSeleccionado, 2)); ?></strong></td>
                            <?php break; ?>
                        <?php case (6): ?>
                            <td style="text-align: center;border-bottom: none;border-top: none;background-color: #e1e1e1;"></td>
                            <?php break; ?>
                        <?php case (8): ?>
                            <td style="text-align: center;border-top: none;background-color: #e1e1e1;"></td>
                            <?php break; ?>

                    <?php endswitch; ?>


            </tr>
            <?php
                // Incrementar el índice para evitar procesar el mismo elemento
                $i++; // Aumentar aquí para que el bucle for avance más rápido
            ?>
        <?php endfor; ?>
        <?php
            $total += $puntajeCargoSeleccionado;
            $primerOtros = $otroMaximos->first();
        ?>
        <tr style="background-color: #e1e1e1;">
            <td style="text-align: center;"><?php echo e($primerOtros->padre_nombre); ?></td><td colspan="4" style="text-align: center;">OTROS ANTECEDENTES</td>
        </tr>
        <?php

            $totalGrupo=0;
            $subPuntaje=0;
            $totalItem=0;
            $submax = 0;
            $sub = 0;
            $j = 0;
        ?>
        <?php $__currentLoopData = $otroMaximos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $otroMaximo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($submax != $otroMaximo->evaluacion_grupo_id): ?>
                <?php if($loop->index != 0 && $otroMaximos[$loop->index - 1]->grupo_maximo != 0): ?>
                    <?php
                        $subPuntaje = ($subPuntaje>$otroMaximos[$loop->index - 1]->grupo_maximo)?$otroMaximos[$loop->index - 1]->grupo_maximo:$subPuntaje;
                    ?>
                    <td colspan="4" style="text-align: right;background-color: #e1e1e1;">Subtotal (max. <?php echo e($otroMaximos[$loop->index - 1]->grupo_maximo); ?> )</td><td style="text-align: center;background-color: #e1e1e1;"><strong><?php echo e(number_format($subPuntaje, 2)); ?> </strong></td>

                <?php endif; ?>
                <?php
                    $submax = $otroMaximo->evaluacion_grupo_id;
                ?>
            <?php endif; ?>
            <?php if($sub != $otroMaximo->evaluacion_subgrupo_id && $otroMaximo->evaluacion_subgrupo_id): ?>
                <?php
                    $j++;
                    $totalGrupo += $subPuntaje;
                    $subPuntaje=0;
                ?>
                <tr>
                    <td style="text-align: center;background-color: #e1e1e1;"><?php echo e($otroMaximo->padre_nombre); ?><?php echo e($j); ?></td>
                    <td colspan="4" style="background-color: #e1e1e1;text-align: left;"><?php echo e($otroMaximo->subgrupo_nombre); ?></td>
                </tr>
                <?php
                    $sub = $otroMaximo->evaluacion_subgrupo_id;
                ?>
            <?php elseif(!$otroMaximo->evaluacion_subgrupo_id): ?>
                <?php
                    $j++;
                ?>
            <?php endif; ?>
                <?php
                    $puntajeOtro = $evaluacion->puntaje_otros->where('joven_evaluacion_planilla_otro_max_id', $otroMaximo->id)->first();
                    $puntaje = (($puntajeOtro)&&($puntajeOtro->puntaje))?(int)$puntajeOtro->puntaje:'';
                    $tope = (($otroMaximo->tope==0)||($otroMaximo->tope==$otroMaximo->minimo))?'':'<strong>Max. '.$otroMaximo->tope.'pt.</strong>';
                    $hasta = (($otroMaximo->maximo!=0)&&($otroMaximo->minimo==$otroMaximo->maximo))?((($otroMaximo->minimo==$otroMaximo->tope)&&($otroMaximo->minimo==$otroMaximo->maximo))?$otroMaximo->maximo. ' pt.':$otroMaximo->maximo. ' c/u'):'Hasta '.$otroMaximo->tope;
                    $control = (($otroMaximo->maximo!=0)&&($otroMaximo->minimo==$otroMaximo->maximo))?((($otroMaximo->minimo==$otroMaximo->tope)&&($otroMaximo->minimo==$otroMaximo->maximo))?1:2):3; //1=check 2=c/u 3=hasta
                    switch ( $control) {
                        case '1' :
                            $totalItem = ($puntajeOtro)?$puntaje:0;

                        break;
                        case '2' :
                            $totalItem = ($puntajeOtro)?$puntaje*$otroMaximo->maximo:0;
                            if($otroMaximo->tope && $totalItem>$otroMaximo->tope){
                                $totalItem = $otroMaximo->tope;
                            }
                        break;
                        case '3' :
                            $totalItem = ($puntajeOtro)?$puntaje:0;
                            if($otroMaximo->tope && $totalItem>$otroMaximo->tope){
                                $totalItem = $otroMaximo->tope;
                            }
                        break;

                    }
                    $descripcion = str_replace('#puntaje#', '<B>'.$otroMaximo->maximo.' puntos</B>',$otroMaximo->otro_nombre);
                ?>
            <tr>
                <?php if($otroMaximo->evaluacion_subgrupo_id): ?>
                    <td style="text-align: center;background-color: #e1e1e1;"><?php echo $tope; ?></td>
                <?php else: ?>
                    <td style="text-align: center;background-color: #e1e1e1;"><?php echo e($otroMaximo->padre_nombre); ?><?php echo e($j); ?><br><?php echo $tope; ?></td>
                <?php endif; ?>
                <td style="text-align: left;width: 55%"><?php echo $descripcion; ?></td>
                <td style="text-align: right">
                    <?php echo $hasta; ?>

                </td>
                <td style="text-align: right;">
                    <?php echo e($puntaje); ?>

                </td>
                <td style="text-align: center;">
                    <strong> <?php echo e(number_format($totalItem, 2)); ?></strong>
                </td>
            </tr>
                <?php
                    //$totalGrupo += $totalItem;

                    $subPuntaje += $totalItem;

                ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php

            $ultimoOtroMaximo = $otroMaximos->last();
            //dd($ultimoAntAcadMaximo);
        ?>
        <?php if($ultimoOtroMaximo->grupo_maximo!=0): ?>
            <?php
                $subPuntaje = ($subPuntaje>$ultimoOtroMaximo->grupo_maximo)?$ultimoOtroMaximo->grupo_maximo:$subPuntaje;
            ?>
            <tr>
                <td colspan="4" style="text-align: right;background-color: #e1e1e1;"> Subtotal (max. <?php echo e($ultimoOtroMaximo->grupo_maximo); ?>)</td><td style="text-align: center;background-color: #e1e1e1;"><strong><?php echo e(number_format($subPuntaje, 2)); ?></strong></td>
            </tr>
        <?php endif; ?>
        <?php if($ultimoOtroMaximo->padre_id): ?>
            <?php
                $totalGrupo += $subPuntaje;
               $totalGrupo = ($totalGrupo>$ultimoOtroMaximo->padre_maximo)?$ultimoOtroMaximo->padre_maximo:$totalGrupo;
            ?>
            <tr>
                <td colspan="4" style="text-align: right;background-color: #e1e1e1;"> Subtotal <?php echo e($ultimoOtroMaximo->padre_nombre); ?> (max. <?php echo e($ultimoOtroMaximo->padre_maximo); ?>)</td><td style="text-align: center;background-color: #e1e1e1;"><strong><?php echo e(number_format($totalGrupo, 2)); ?></strong></td>
            </tr>
        <?php endif; ?>
        <?php
            $total += $totalGrupo;
            $primerProduccion = $produccionMaximos->first();
            $periodoActual =intval($year);
        ?>
        <tr style="background-color: #e1e1e1;">
            <td style="text-align: center;"><?php echo e($primerProduccion->padre_nombre); ?></td><td colspan="4" style="text-align: center;">FORMACI&Oacute;N DE RR.HH. Y PRODUCCI&Oacute;N CIENT&Iacute;FICA EN LOS ULTIMOS 5 A&Ntilde;OS  (<?php echo e($periodoActual-4); ?>, <?php echo e($periodoActual-3); ?>, <?php echo e($periodoActual-2); ?>, <?php echo e($periodoActual-1); ?>, <?php echo e($periodoActual); ?>)</td>
        </tr>
        <?php

            $totalGrupo=0;
            $subPuntaje=0;
            $totalItem=0;
            $submax = 0;
            $sub = 0;
            $j = 0;
        ?>
        <?php $__currentLoopData = $produccionMaximos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produccionMaximo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($submax != $produccionMaximo->evaluacion_grupo_id): ?>
                <?php if($loop->index != 0 && $produccionMaximos[$loop->index - 1]->grupo_maximo != 0): ?>
                    <tr>
                        <?php
                            $subPuntaje = ($subPuntaje>$produccionMaximos[$loop->index - 1]->grupo_maximo)?$produccionMaximos[$loop->index - 1]->grupo_maximo:$subPuntaje;
                        ?>
                        <td colspan="4" style="text-align: right;background-color: #e1e1e1;">Subtotal (max. <?php echo e($produccionMaximos[$loop->index - 1]->grupo_maximo); ?> )</td><td style="text-align: center;background-color: #e1e1e1;"><strong><?php echo e(number_format($subPuntaje, 2)); ?> </strong></td>
                    </tr>
                <?php endif; ?>
                <?php
                    $submax = $produccionMaximo->evaluacion_grupo_id;
                ?>
            <?php endif; ?>
            <?php if($sub != $produccionMaximo->evaluacion_subgrupo_id && $produccionMaximo->evaluacion_subgrupo_id): ?>
                <?php
                    $j++;
                    $totalGrupo += $subPuntaje;
                    $subPuntaje=0;
                ?>
                <tr>
                    <td style="text-align: center;background-color: #e1e1e1;"><?php echo e($produccionMaximo->padre_nombre); ?><?php echo e($j); ?></td>
                    <td colspan="4" style="background-color: #e1e1e1;text-align: left;"><?php echo e($produccionMaximo->subgrupo_nombre); ?></td>
                </tr>
                <?php
                    $sub = $produccionMaximo->evaluacion_subgrupo_id;
                ?>
            <?php elseif(!$produccionMaximo->evaluacion_subgrupo_id): ?>
                <?php
                    $j++;
                ?>
            <?php endif; ?>
            <?php
                $puntajeProduccion = $evaluacion->puntaje_produccions->where('joven_evaluacion_planilla_produccion_max_id', $produccionMaximo->id)->first();
                $puntaje = (($puntajeProduccion)&&($puntajeProduccion->puntaje))?(int)$puntajeProduccion->puntaje:'';
                $tope = (($produccionMaximo->tope==0)||($produccionMaximo->tope==$produccionMaximo->minimo))?'':'<strong>Max. '.$produccionMaximo->tope.'pt.</strong>';
                $hasta = (($produccionMaximo->maximo!=0)&&($produccionMaximo->minimo==$produccionMaximo->maximo))?((($produccionMaximo->minimo==$produccionMaximo->tope)&&($produccionMaximo->minimo==$produccionMaximo->maximo))?$produccionMaximo->maximo. ' pt.':$produccionMaximo->maximo. ' c/u'):'Hasta '.$produccionMaximo->tope;
                $control = (($produccionMaximo->maximo!=0)&&($produccionMaximo->minimo==$produccionMaximo->maximo))?((($produccionMaximo->minimo==$produccionMaximo->tope)&&($produccionMaximo->minimo==$produccionMaximo->maximo))?1:2):3; //1=check 2=c/u 3=hasta
                switch ( $control) {
                    case '1' :
                        $totalItem = ($puntajeProduccion)?$puntaje:0;

                    break;
                    case '2' :
                        $totalItem = ($puntajeProduccion)?$puntaje*$produccionMaximo->maximo:0;
                        if($produccionMaximo->tope && $totalItem>$produccionMaximo->tope){
                            $totalItem = $produccionMaximo->tope;
                        }
                    break;
                    case '3' :
                        $totalItem = ($puntajeProduccion)?$puntaje:0;
                        $puntaje = ($puntajeProduccion)?$puntajeProduccion->cantidad:0;
                        if($produccionMaximo->tope && $totalItem>$produccionMaximo->tope){

                            $totalItem = $produccionMaximo->tope;
                        }
                    break;

                }
                $descripcion = str_replace('#puntaje#', '<B>'.$produccionMaximo->maximo.' puntos</B>',$produccionMaximo->produccion_nombre);
            ?>
            <tr>
                <?php if($produccionMaximo->evaluacion_subgrupo_id): ?>
                    <td style="text-align: center;background-color: #e1e1e1;"><?php echo $tope; ?></td>
                <?php else: ?>
                    <td style="text-align: center;background-color: #e1e1e1;"><?php echo e($produccionMaximo->padre_nombre); ?><?php echo e($j); ?><br><?php echo $tope; ?></td>
                <?php endif; ?>
                <td style="text-align: left;width: 55%"><?php echo $descripcion; ?></td>
                <td style="text-align: right">
                    <?php echo $hasta; ?>

                </td>
                <td style="text-align: right;">
                    <?php echo e($puntaje); ?>

                </td>
                <td style="text-align: center;">
                    <strong> <?php echo e(number_format($totalItem, 2)); ?></strong>
                </td>
            </tr>
            <?php
                //$totalGrupo += $totalItem;

                $subPuntaje += $totalItem;

            ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php

            $ultimoProduccionMaximo = $produccionMaximos->last();
            //dd($ultimoAntAcadMaximo);
        ?>
        <?php if($ultimoProduccionMaximo->grupo_maximo!=0): ?>
            <?php
                $subPuntaje = ($subPuntaje>$ultimoProduccionMaximo->grupo_maximo)?$ultimoProduccionMaximo->grupo_maximo:$subPuntaje;
            ?>
            <tr>
                <td colspan="4" style="text-align: right;background-color: #e1e1e1;"> Subtotal (max. <?php echo e($ultimoProduccionMaximo->grupo_maximo); ?>)</td><td style="text-align: center;background-color: #e1e1e1;"><strong><?php echo e(number_format($subPuntaje, 2)); ?></strong></td>
            </tr>
        <?php endif; ?>
        <?php if($ultimoProduccionMaximo->padre_id): ?>
            <?php
                $totalGrupo += $subPuntaje;
               $totalGrupo = ($totalGrupo>$ultimoProduccionMaximo->padre_maximo)?$ultimoProduccionMaximo->padre_maximo:$totalGrupo;
            ?>
            <tr>
                <td colspan="4" style="text-align: right;background-color: #e1e1e1;"> Subtotal <?php echo e($ultimoProduccionMaximo->padre_nombre); ?> (max. <?php echo e($ultimoProduccionMaximo->padre_maximo); ?>)</td><td style="text-align: center;background-color: #e1e1e1;"><strong><?php echo e(number_format($totalGrupo, 2)); ?></strong></td>
            </tr>
        <?php endif; ?>
        <?php
            $total += $totalGrupo;

            $primerAnterior = $anteriorMaximos->first();

        ?>

        <?php

            $totalGrupo=0;
            $subPuntaje=0;
            $totalItem=0;
            $submax = 0;
            $sub = 0;
            $j = 0;
        ?>
        <?php $__currentLoopData = $anteriorMaximos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anteriorMaximo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <?php if($submax != $anteriorMaximo->evaluacion_grupo_id): ?>
                <?php if($loop->index != 0 && $anteriorMaximos[$loop->index - 1]->grupo_maximo != 0): ?>
                    <tr>
                        <?php
                            $subPuntaje = ($subPuntaje>$anteriorMaximos[$loop->index - 1]->grupo_maximo)?$anteriorMaximos[$loop->index - 1]->grupo_maximo:$subPuntaje;
                        ?>
                        <td colspan="4" style="text-align: right;background-color: #e1e1e1;">Subtotal (max. <?php echo e($anteriorMaximos[$loop->index - 1]->grupo_maximo); ?> )</td><td style="text-align: center;background-color: #e1e1e1;"><strong><?php echo e(number_format($subPuntaje, 2)); ?> </strong></td>
                    </tr>
                <?php endif; ?>
                <?php
                    $submax = $anteriorMaximo->evaluacion_grupo_id;
                ?>
            <?php endif; ?>
            <?php if($sub != $anteriorMaximo->evaluacion_subgrupo_id && $anteriorMaximo->evaluacion_subgrupo_id): ?>
                <?php
                    $j++;
                    $totalGrupo += $subPuntaje;
                    $subPuntaje=0;
                ?>

                <?php
                    $sub = $anteriorMaximo->evaluacion_subgrupo_id;
                ?>
            <?php elseif(!$anteriorMaximo->evaluacion_subgrupo_id): ?>
                <?php
                    $j++;
                ?>
            <?php endif; ?>
            <?php
                $puntajeAnterior = $evaluacion->puntaje_anteriors->where('joven_evaluacion_planilla_anterior_max_id', $anteriorMaximo->id)->first();
                $puntaje = (($puntajeAnterior)&&($puntajeAnterior->puntaje))?(int)$puntajeAnterior->puntaje:'';
                $tope = (($anteriorMaximo->tope==0)||($anteriorMaximo->tope==$anteriorMaximo->minimo))?'':'<strong>Max. '.$anteriorMaximo->tope.'pt.</strong>';
                ///dd($tope);
                $hasta = (($anteriorMaximo->maximo!=0)&&($anteriorMaximo->minimo==$anteriorMaximo->maximo))?((($anteriorMaximo->minimo==$anteriorMaximo->tope)&&($anteriorMaximo->minimo==$anteriorMaximo->maximo))?$anteriorMaximo->maximo. ' pt.':$anteriorMaximo->maximo. ' c/u'):'Hasta '.$anteriorMaximo->tope;
                $control = (($anteriorMaximo->maximo!=0)&&($anteriorMaximo->minimo==$anteriorMaximo->maximo))?((($anteriorMaximo->minimo==$anteriorMaximo->tope)&&($anteriorMaximo->minimo==$anteriorMaximo->maximo))?1:2):3; //1=check 2=c/u 3=hasta
                switch ( $control) {
                    case '1' :
                        $totalItem = ($puntajeAnterior)?$puntaje:0;

                    break;
                    case '2' :
                        $totalItem = ($puntajeAnterior)?$puntaje*$anteriorMaximo->maximo:0;
                        if($anteriorMaximo->tope && $totalItem>$anteriorMaximo->tope){
                            $totalItem = $anteriorMaximo->tope;
                        }
                    break;
                    case '3' :
                        $totalItem = ($puntajeAnterior)?$puntaje:0;
                        if($anteriorMaximo->tope && $totalItem>$anteriorMaximo->tope){
                            $totalItem = $anteriorMaximo->tope;
                        }
                    break;

                }
                $descripcion = str_replace('#puntaje#', '<B>'.$anteriorMaximo->maximo.' puntos</B>',$anteriorMaximo->anterior_nombre);
            ?>
            <tr>

                    <td style="text-align: center;background-color: #e1e1e1;">E<?php echo e($j); ?><br><?php echo $tope; ?></td>

                <td style="text-align: left;width: 55%"><?php echo $descripcion; ?></td>
                <td style="text-align: right">
                    <?php echo $hasta; ?>

                </td>
                <td style="text-align: right;">
                    <?php echo e($puntaje); ?>

                </td>
                <td style="text-align: center;">
                    <strong> <?php echo e(number_format($totalItem, 2)); ?></strong>
                </td>
            </tr>
            <?php
                //$totalGrupo += $totalItem;

                $subPuntaje += $totalItem;

            ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php
            $totalGrupo += $subPuntaje;
            $total += $totalGrupo;

            $primerJustificacion = $justificacionMaximos->first();

        ?>
        <tr style="background-color: #e1e1e1;">
            <td style="text-align: center;">F</td><td colspan="4" style="text-align: center;">JUSTIFICACIÓN TÉCNICA DEL SUBSIDIO SOLICITADO</td>
        </tr>
        <?php

            $totalGrupo=0;
            $subPuntaje=0;
            $totalItem=0;
            $submax = 0;
            $sub = 0;
            $j = 0;
        ?>
        <?php $__currentLoopData = $justificacionMaximos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $justificacionMaximo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($submax != $justificacionMaximo->evaluacion_grupo_id): ?>
                <?php if($loop->index != 0 && $justificacionMaximos[$loop->index - 1]->grupo_maximo != 0): ?>
                    <tr>
                        <?php
                            $subPuntaje = ($subPuntaje>$justificacionMaximos[$loop->index - 1]->grupo_maximo)?$justificacionMaximos[$loop->index - 1]->grupo_maximo:$subPuntaje;
                        ?>
                        <td colspan="4" style="text-align: right;background-color: #e1e1e1;">Subtotal (max. <?php echo e($justificacionMaximos[$loop->index - 1]->grupo_maximo); ?> )</td><td style="text-align: center;background-color: #e1e1e1;"><strong><?php echo e(number_format($subPuntaje, 2)); ?> </strong></td>
                    </tr>
                <?php endif; ?>
                <?php
                    $submax = $justificacionMaximo->evaluacion_grupo_id;
                ?>
            <?php endif; ?>
            <?php if($sub != $justificacionMaximo->evaluacion_subgrupo_id && $justificacionMaximo->evaluacion_subgrupo_id): ?>
                <?php
                    $j++;
                    $totalGrupo += $subPuntaje;
                    $subPuntaje=0;
                ?>
                <tr>
                    <td style="text-align: center;background-color: #e1e1e1;">F<?php echo e($j); ?></td>
                    <td colspan="4" style="background-color: #e1e1e1;text-align: left;"><?php echo e($justificacionMaximo->subgrupo_nombre); ?></td>
                </tr>
                <?php
                    $sub = $justificacionMaximo->evaluacion_subgrupo_id;
                ?>
            <?php elseif(!$justificacionMaximo->evaluacion_subgrupo_id): ?>
                <?php
                    $j++;
                ?>
            <?php endif; ?>
            <?php
                $puntajeJustificacion = $evaluacion->puntaje_justificacions->where('joven_evaluacion_planilla_justificacion_max_id', $justificacionMaximo->id)->first();
                $puntaje = (($puntajeJustificacion)&&($puntajeJustificacion->puntaje))?(int)$puntajeJustificacion->puntaje:'';
                $tope = (($justificacionMaximo->tope==0)||($justificacionMaximo->tope==$justificacionMaximo->minimo))?'':'<strong>Max. '.$justificacionMaximo->tope.'pt.</strong>';
                $hasta = (($justificacionMaximo->maximo!=0)&&($justificacionMaximo->minimo==$justificacionMaximo->maximo))?((($justificacionMaximo->minimo==$justificacionMaximo->tope)&&($justificacionMaximo->minimo==$justificacionMaximo->maximo))?$justificacionMaximo->maximo. ' pt.':$justificacionMaximo->maximo. ' c/u'):'Hasta '.$justificacionMaximo->tope;
                $control = (($justificacionMaximo->maximo!=0)&&($justificacionMaximo->minimo==$justificacionMaximo->maximo))?((($justificacionMaximo->minimo==$justificacionMaximo->tope)&&($justificacionMaximo->minimo==$justificacionMaximo->maximo))?1:2):3; //1=check 2=c/u 3=hasta
                switch ( $control) {
                    case '1' :
                        $totalItem = ($puntajeJustificacion)?$puntaje:0;

                    break;
                    case '2' :
                        $totalItem = ($puntajeJustificacion)?$puntaje*$justificacionMaximo->maximo:0;
                        if($justificacionMaximo->tope && $totalItem>$justificacionMaximo->tope){
                            $totalItem = $justificacionMaximo->tope;
                        }
                    break;
                    case '3' :
                        $totalItem = ($puntajeJustificacion)?$puntaje:0;
                        if($justificacionMaximo->tope && $totalItem>$justificacionMaximo->tope){
                            $totalItem = $justificacionMaximo->tope;
                        }
                    break;

                }
                $descripcion = str_replace('#puntaje#', '<B>'.$justificacionMaximo->maximo.' puntos</B>',$justificacionMaximo->justificacion_nombre);
            ?>
            <tr>
                <?php if($justificacionMaximo->evaluacion_subgrupo_id): ?>
                    <td style="text-align: center;background-color: #e1e1e1;"><?php echo $tope; ?></td>
                <?php else: ?>
                    <td style="text-align: center;background-color: #e1e1e1;">F<?php echo e($j); ?><br><?php echo $tope; ?></td>
                <?php endif; ?>
                <td style="text-align: left;width: 55%"><?php echo $descripcion; ?></td>
                <td style="text-align: right">
                    <?php echo $hasta; ?>

                </td>
                <td style="text-align: right;">
                    <?php echo e($puntaje); ?>

                </td>
                <td style="text-align: center;">
                    <strong> <?php echo e(number_format($totalItem, 2)); ?></strong>
                </td>
            </tr>
            <?php
                //$totalGrupo += $totalItem;

                $subPuntaje += $totalItem;

            ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <?php
            $totalGrupo += $subPuntaje;
                $total += $totalGrupo;



        ?>
    <tr>
        <td colspan="4" style="text-align: right;"><strong> Total (max. <span><?php echo e($planilla->maximo); ?></span> )</strong></td><td style="text-align: center;background-color: #e1e1e1;"><strong><?php echo e(number_format($total, 2)); ?></strong></td>
    </tr>
</table>

</div>
<div class="content" style="margin-top: 50px;">
    <div style="background-color: #000;color:#FFF">Observaciones</div>
</div>
<div class="content">
    <span style="display: inline-block; border: 1px solid #ccc;width: 695px; padding-top: <?php echo e($evaluacion->observaciones ? '0' : '15px'); ?>;"><?php echo e($evaluacion->observaciones); ?></span>
</div>

<div class="signature-aclaracion">
    <div class="signature-no-line"></div>
    <div class="signature-no-line"><?php echo e($evaluador); ?></div>
</div>

<div class="signature">
    <div class="signature-line">Firma</div>
    <div class="signature-line">Aclaración</div>
</div>

</body>
</html>
<?php /**PATH /var/www/sicadi/resources/views/joven_evaluacions/pdfevaluacion.blade.php ENDPATH**/ ?>