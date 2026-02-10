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
            flex: 1;
            display: table-cell; /* Celda de la tabla para la imagen */
            width: 50px;
            height: 50px;
            vertical-align: top;
        }

        .text-content {
            display: table-cell; /* Celda de tabla para los textos */
            vertical-align: top; /* Alinea los textos en la parte superior */
            padding-left: 10px; /* Espaciado entre celdas */
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

        table {
            page-break-inside: avoid;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
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

            <img src="{{ public_path('/images/unlp.jpg') }}" alt="UNLP Logo" class="logo">

        </div>
        <div class="text-content" >
            <div class="title">
                SOLICITUD DE SUBSIDIOS {{ $year }} {{ $year+1 }}
            </div>
            <div class="subtitle">
                Viajes/Estadías<br>
                <strong>PLANILLA DE EVALUACION</strong>
            </div>
        </div>
        <div class="text-content" style="text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div class="title" style="text-align: center;">
                Motivo de la Solicitud
            </div>
            <table style="width: 100%; text-align: center;">
                <tr>
                    <td style="vertical-align: middle; height: 30px;">
                        <div class="subtitle" style="font-size: 18px">
                            {{$motivo}} {{$tipo}}
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

    </div>
    <div class="content">

            <span style="display: inline-block;width: 125px;vertical-align: middle;">Apellido y Nombres</span> <span style="display: inline-block; width: 205px; padding-top: {{ $solicitante ? '0' : '15px' }};vertical-align: middle;">{{ $solicitante }}</span>
            <span style="display: inline-block;width: 90px;vertical-align: middle;">U. Académica</span> <span style="display: inline-block; width: 265px; padding-top: {{ $facultadplanilla ? '0' : '15px' }};vertical-align: middle; ">{{ $facultadplanilla }}</span>


    </div>
    <div style="clear: both;"></div>
</div>
<div class="content">
    <table>

    <tr style="background-color: #000;color:#FFF">
        <th>P. Max/ITEM</th><th colspan="3">DETALLE Y PUNTAJE</th><th>P. OTORGADO</th>
    </tr>
        @php

             $total = 0;
        @endphp

        @php
            $primerCategoria = $categoriaMaximos->first();
            $puntajeCategoria = $evaluacion->puntaje_categorias->first();
             //dd($puntajeCategoria);
             $puntajeCategoriaSeleccionado = 0;
             foreach ($categoriaMaximos as $categoriaMaximo){
                 if(($puntajeCategoria) && ($puntajeCategoria->viaje_evaluacion_planilla_categoria_max_id == $categoriaMaximo->id)){
                    $puntajeCategoriaSeleccionado = $categoriaMaximo->maximo;
                }
             }


        @endphp

        <tr>
            <td style="text-align: center;background-color: #e1e1e1; width: 15%">CATEGORIA <br><strong>Max. {{ $maxCategoria }} pt.</strong></td>





            <td colspan="3" >
                @php
                    // Contar el número de elementos
                    $numElementos = count($categoriaMaximos);
                    // Calcular el ancho dinámico (un máximo de 6 elementos por fila para mantener legibilidad)
                    $anchoElemento = $numElementos > 6 ? 16 : (100 / $numElementos) - 2; // Espacio ajustado entre elementos
                @endphp

                @foreach($categoriaMaximos as $categoriaMaximo)
                    @php
                        $imgSINO = (($puntajeCategoria) && ($puntajeCategoria->viaje_evaluacion_planilla_categoria_max_id == $categoriaMaximo->id)) ? 'si' : 'no';
                        $imgSrc = asset('images/' . $imgSINO . '.jpg');
                    @endphp

                    <div style="float: left; width: {{ $anchoElemento }}%; text-align: center; margin-bottom: 15px;">
                        <img src="{{ $imgSrc }}" alt="{{ $imgSINO == 'no' ? 'No' : 'Sí' }}">
                        <br>
                        {{ $categoriaMaximo->categoria_nombre }} ({{ $categoriaMaximo->maximo }} pt.)
                    </div>
                @endforeach
            </td>










            <td style="text-align: center;background-color: #e1e1e1; width: 15%"><strong>{{ number_format($puntajeCategoriaSeleccionado, 2) }}</strong></td>
        </tr>
    </table>
    <table>
        @php
            $total += $puntajeCategoriaSeleccionado;

            $puntajeCargo = $evaluacion->puntaje_cargos->first();
            $puntajeCargoSeleccionado = 0;

            foreach ($cargoMaximos as $cargoMaximo) {
                if (($puntajeCargo) && ($puntajeCargo->viaje_evaluacion_planilla_cargo_max_id == $cargoMaximo->id)) {
                    $puntajeCargoSeleccionado = $cargoMaximo->maximo;
                }
            }

            $filas = ceil(count($cargoMaximos) / 2); // Número de filas necesarias (mostrando de a 2 por fila)
        @endphp

        @for ($i = 0; $i < $filas; $i++)
            <tr>
                @if ($i == 0)
                    <!-- Primera celda con rowspan para el título -->
                    <td style="text-align: center; background-color: #e1e1e1; width: 15%;" rowspan="{{ $filas }}">
                        CARGO DOCENTE ACTUAL EN LA UNLP<br>
                        <strong>Max. {{ $maxCargo }} pt.</strong>
                    </td>
                @endif

                <!-- Celdas intermedias para los cargos -->
                <td colspan="3" style="text-align: left;
                       border-right: none;
                       {{ $i == 0 ? 'border-top: 1px solid black;border-bottom:none' : 'border-top:none;border-bottom:none' }}
                       {{ $i == $filas - 1 ? 'border-bottom: 1px solid black;border-top:none' : 'border-top:none;border-bottom:none' }}">
                    @for ($j = 0; $j < 2; $j++)
                        @php
                            $index = ($i * 2) + $j;
                            $cargoActual = $cargoMaximos[$index] ?? null;
                        @endphp

                        @if ($cargoActual)
                            @php
                                $imgSINO = (($puntajeCargo) && ($puntajeCargo->viaje_evaluacion_planilla_cargo_max_id == $cargoActual->id)) ? 'si' : 'no';
                                $imgSrc = asset('/images/' . $imgSINO . '.jpg');
                            @endphp

                            <div style="width: 50%; float: left; text-align: left; border-right: none;">
                                <img src="{{ $imgSrc }}" alt="{{ $imgSINO == 'no' ? 'No' : 'Sí' }}" style="vertical-align: middle;">
                                {{ $cargoActual->cargo_nombre }} ({{ $cargoActual->maximo }} pt.)
                            </div>
                        @endif
                    @endfor
                </td>

                @if ($i == 0)
                    <!-- Última celda con rowspan para el puntaje seleccionado -->
                    <td style="text-align: center; background-color: #e1e1e1; width: 15%" rowspan="{{ $filas }}">
                        <strong>{{ number_format($puntajeCargoSeleccionado, 2) }}</strong>
                    </td>
                @endif
            </tr>
        @endfor
    </table>

    <table>

        @php
            $total += $puntajeCargoSeleccionado;
           // $primerOtros = $otroMaximos->first();
        @endphp

        @php

            $totalGrupo=0;
            $subPuntaje=0;
            $totalItem=0;
            $submax = 0;
            $sub = 0;
            $j = 0;
            $iterador1=$planilla->iterador1;
            $iterador2=$planilla->iterador2;
            $primeritems = $itemMaximos->first();
            $maxGrupoSiguiente = 0;//como voy recorrrelo todo aprovecho a calcular el maximo de los RRHH
        @endphp
        @foreach($itemMaximos as $index => $itemMaximo)
            {{-- Primera iteración: Hasta iterador1 --}}
            @if($index < $planilla->iterador1)
                @if ($submax != $itemMaximo->evaluacion_grupo_id)
                    @if ($loop->index != 0 && $itemMaximos[$loop->index - 1]->grupo_maximo != 0)
                        @php
                            $subPuntaje = ($subPuntaje>$itemMaximos[$loop->index - 1]->grupo_maximo)?$itemMaximos[$loop->index - 1]->grupo_maximo:$subPuntaje;
                        @endphp
                        <td colspan="3" style="text-align: right;background-color: #e1e1e1;">Subtotal (max. {{ $itemMaximos[$loop->index - 1]->grupo_maximo }} )</td><td style="text-align: center;background-color: #e1e1e1;"><strong>{{ number_format($subPuntaje, 2)}} </strong></td>

                    @endif
                    @php
                        $submax = $itemMaximo->evaluacion_grupo_id;
                    @endphp
                @endif
                    @if ($sub != $itemMaximo->evaluacion_grupo_id && $itemMaximo->evaluacion_grupo_id)
                        @php
                            $j++;
                            $totalGrupo += $subPuntaje;
                            $subPuntaje=0;
                        @endphp

                        @php
                            $sub = $itemMaximo->evaluacion_grupo_id;
                        @endphp

                    @endif
                @php
                    $puntajeItem = $evaluacion->puntaje_items->where('viaje_evaluacion_planilla_item_max_id', $itemMaximo->id)->first();
                    $cantidad = (($puntajeItem)&&($puntajeItem->cantidad))?(int)$puntajeItem->cantidad:'';
                    $puntaje = (($puntajeItem)&&($puntajeItem->puntaje))?(int)$puntajeItem->puntaje:'';


                    $hasta = $itemMaximo->maximo. ' c/u';

                    $totalItem = ($cantidad)?$cantidad*$itemMaximo->maximo:0;

                    //$totalItem = $puntaje;
                    $descripcion = str_replace('#puntaje#', '<B>'.$itemMaximo->maximo.' puntos</B>',$itemMaximo->item_nombre);
                @endphp
                <tr>
                    @if($primeritems->id == $itemMaximo->id)
                        <td style="text-align: center;background-color: #e1e1e1;width: 15%" rowspan="{{($planilla->iterador1 + 4)}}">PROD. ULTIMOS 5 AÑOS {{($planilla->motivo!='C')?'':' DEL SOLICITANTE'}}<br><strong>(max. {{$itemMaximo->padre_maximo}}pt.)</strong></td>
                    @endif

                    <td style="text-align: left;width: 50%">{!!  $descripcion !!}</td>
                    <td style="text-align: right">
                        {!! $hasta !!}
                    </td>
                    <td style="text-align: right;width: 5%">
                        {{$cantidad}}
                    </td>
                    <td style="text-align: center;width: 15%">
                        <strong> {{ number_format($totalItem, 2) }}</strong>
                    </td>
                </tr>
                @php
                    //$totalGrupo += $totalItem;
                    //Log::info("Sub antes: " . $subPuntaje . " - item: " . $totalItem);
                    $subPuntaje += $totalItem;
                    //Log::info("Sub: " . $subPuntaje);
                @endphp
            @else
                @php
                    if ($submax!=$itemMaximo->evaluacion_grupo_id ){
                        $maxGrupoSiguiente +=$itemMaximo->grupo_maximo;
                    }
                    $submax = $itemMaximo->evaluacion_grupo_id;
                @endphp
            @endif
        @endforeach
        @php

            $ultimoItemMaximo=$itemMaximos->take($planilla->iterador1)->last();
            Log::info("Maximos: " . print_r($itemMaximos, true));
            //dd($ultimoAntAcadMaximo);
        @endphp
        @if($ultimoItemMaximo->grupo_maximo!=0)
            @php
                $subPuntaje = ($subPuntaje>$ultimoItemMaximo->grupo_maximo)?$ultimoItemMaximo->grupo_maximo:$subPuntaje;
                //Log::info("Sub def: " . $subPuntaje);
            @endphp
            <tr>
                <td colspan="3" style="text-align: right;background-color: #e1e1e1;"> Subtotal (max. {{$ultimoItemMaximo->grupo_maximo}})</td><td style="text-align: center;background-color: #e1e1e1;"><strong>{{ number_format($subPuntaje, 2) }}</strong></td>
            </tr>
        @endif
        @if($primeritems->padre_id)
            @php
                //Log::info("Grupo antes: " . $totalGrupo . " - sub: " . $subPuntaje);
                $totalGrupo += $subPuntaje;
                //Log::info("Grupo: " . $totalGrupo);
               $totalGrupo = ($totalGrupo>$primeritems->padre_maximo)?$primeritems->padre_maximo:$totalGrupo;
                //Log::info("Grupo def: " . $totalGrupo);
            @endphp
            <tr>
                <td colspan="3" style="text-align: right;background-color: #e1e1e1;"> Subtotal PROD. ULTIMOS 5 AÑOS {{($planilla->motivo!='C')?'':' DEL SOLICITANTE'}} (max.{{$primeritems->padre_maximo}})</td></td><td style="text-align: center;background-color: #e1e1e1;"><strong>{{ number_format($totalGrupo, 2) }}</strong></td>
            </tr>
        @endif
    </table>
    <table>
        @php
            $total += $totalGrupo;
            //Log::info("Total: " . $total);
        @endphp
        @php

            $totalGrupo=0;
            $subPuntaje=0;
            $totalItem=0;
            $submax = 0;
            $sub = 0;
            $j = 0;

            $primeritems = $itemMaximos->take($planilla->iterador1+1)->last();
        @endphp
        @foreach($itemMaximos as $index => $itemMaximo)
            {{-- Primera iteración: Hasta iterador1 --}}
            @if($index >= $planilla->iterador1 && $index < $planilla->iterador2)
                @if ($submax != $itemMaximo->evaluacion_grupo_id)
                    @if ($loop->index != 0 && $loop->index != $planilla->iterador1 && $itemMaximos[$loop->index - 1]->grupo_maximo != 0)
                        @php
                            $subPuntaje = ($subPuntaje>$itemMaximos[$loop->index - 1]->grupo_maximo)?$itemMaximos[$loop->index - 1]->grupo_maximo:$subPuntaje;
                        @endphp
                        <td colspan="3" style="text-align: right;background-color: #e1e1e1;">Subtotal (max. {{ $itemMaximos[$loop->index - 1]->grupo_maximo }} )</td><td style="text-align: center;background-color: #e1e1e1;"><strong>{{ number_format($subPuntaje, 2)}} </strong></td>

                    @endif
                    @php
                        $submax = $itemMaximo->evaluacion_grupo_id;
                    @endphp
                @endif
                @if ($sub != $itemMaximo->evaluacion_grupo_id && $itemMaximo->evaluacion_grupo_id)
                    @php
                        $j++;
                        $totalGrupo += $subPuntaje;
                        $subPuntaje=0;
                    @endphp

                    @php
                        $sub = $itemMaximo->evaluacion_grupo_id;
                    @endphp

                @endif
                @php
                    $puntajeItem = $evaluacion->puntaje_items->where('viaje_evaluacion_planilla_item_max_id', $itemMaximo->id)->first();
                    $cantidad = (($puntajeItem)&&($puntajeItem->cantidad))?(int)$puntajeItem->cantidad:'';
                    $puntaje = (($puntajeItem)&&($puntajeItem->puntaje))?(int)$puntajeItem->puntaje:'';


                    $hasta = $itemMaximo->maximo. ' c/u';

                    $totalItem = ($cantidad)?$cantidad*$itemMaximo->maximo:0;

                    $descripcion = str_replace('#puntaje#', '<B>'.$itemMaximo->maximo.' puntos</B>',$itemMaximo->item_nombre);
                @endphp
                <tr>
                    @if($primeritems->id == $itemMaximo->id)
                        <td style="text-align: center;background-color: #e1e1e1;width: 15%" rowspan="{{(($planilla->iterador2-$planilla->iterador1) + 3)}}">FORMACION RECURSOS HUMANOS<br><strong>(max. {{$maxGrupoSiguiente}}pt.)</strong></td>
                    @endif

                    <td style="text-align: left;width: 50%">{!!  $descripcion !!}</td>
                    <td style="text-align: right">
                        {!! $hasta !!}
                    </td>
                    <td style="text-align: right;width: 5%">
                        {{$cantidad}}
                    </td>
                    <td style="text-align: center;width: 15%">
                        <strong> {{ number_format($totalItem, 2) }}</strong>
                    </td>
                </tr>
                @php
                    //$totalGrupo += $totalItem;

                    //Log::info("Sub antes: " . $subPuntaje . " - item: " . $totalItem);
                    $subPuntaje += $totalItem;
                    //Log::info("Sub: " . $subPuntaje);

                @endphp
            @endif
        @endforeach
        @php

            $ultimoItemMaximo=$itemMaximos->take($planilla->iterador2)->last();
            Log::info("Max: " . $ultimoItemMaximo->grupo_maximo);
            //dd($ultimoitemMaximo);
        @endphp
        @if($ultimoItemMaximo->grupo_maximo!=0)
            @php
                $subPuntaje = ($subPuntaje>$ultimoItemMaximo->grupo_maximo)?$ultimoItemMaximo->grupo_maximo:$subPuntaje;
                //Log::info("Sub def: " . $subPuntaje);
            @endphp
            <tr>
                <td colspan="3" style="text-align: right;background-color: #e1e1e1;"> Subtotal (max. {{$ultimoItemMaximo->grupo_maximo}})</td><td style="text-align: center;background-color: #e1e1e1;"><strong>{{ number_format($subPuntaje, 2) }}</strong></td>
            </tr>
        @endif
        @if($maxGrupoSiguiente)
            @php




               //Log::info("Grupo antes: " . $totalGrupo . " - sub: " . $subPuntaje);
                $totalGrupo += $subPuntaje;
                //Log::info("Grupo: " . $totalGrupo);
               $totalGrupo = ($totalGrupo>$maxGrupoSiguiente)?$maxGrupoSiguiente:$totalGrupo;
                //Log::info("Grupo def: " . $totalGrupo);


            @endphp
            <tr>
                <td colspan="3" style="text-align: right;background-color: #e1e1e1;"> Subtotal FORMACION RECURSOS HUMANOS (max.{{$maxGrupoSiguiente}})</td></td><td style="text-align: center;background-color: #e1e1e1;"><strong>{{ number_format($totalGrupo, 2) }}</strong></td>
            </tr>
        @endif
    </table>
    @php
        $total += $totalGrupo;
        //Log::info("Total: " . $total);
    @endphp
    <table>
            @if($planilla->motivo!='A')
                @php

                    $totalGrupo=0;
                    $subPuntaje=0;
                    $totalItem=0;
                    $submax = 0;
                    $sub = 0;
                    $j = 0;

                   $primerplans = $planMaximos->first();
                   $cantJustificaciones=0;

                @endphp
                @foreach($planMaximos as $planMaximo)//lo recorro 2 veces para saber cuales plans tienen cargados justificación
                @php
                    $puntajePlan = $evaluacion->puntaje_plans->where('viaje_evaluacion_planilla_plan_max_id', $planMaximo->id)->first();

                    if ($puntajePlan && $puntajePlan->justificacion) {
                            $cantJustificaciones++;
                    }
                @endphp

                @endforeach

                @foreach($planMaximos as $planMaximo)


                    @php
                        $puntajePlan = $evaluacion->puntaje_plans->where('viaje_evaluacion_planilla_plan_max_id', $planMaximo->id)->first();
                        $puntaje = (($puntajePlan)&&($puntajePlan->puntaje))?(int)$puntajePlan->puntaje:'';


                        $totalPlan = ($puntajePlan)?$puntaje:0;

                        $descripcion = ($planilla->motivo != 'C')?'PLAN DE TRABAJO':'PLAN DE TRABAJO Y CV DEL VISITANTE<br>';
                        $descripcion_anexo=($planilla->motivo != 'C')?'<ul><li>Objetivo general de la estadía.</li><li>Objetivos específicos de la estadía.</li><li>Plan de trabajo de investigación a realizar en el período.</li><li>Cronograma</li></ul>':'';
                    @endphp
                    <tr>
                        @if($primerplans->id == $planMaximo->id)
                            <td style="text-align: center;background-color: #e1e1e1;width: 15%" rowspan="{{($planMaximos->count()+$cantJustificaciones)}}"><strong>Max. {{$planMaximo->maximo}}pt.</strong></td>
                        @endif

                        <td style="text-align: left;width: 65%" colspan="2">{!!  $descripcion !!}</td>

                        <td style="text-align: right;width: 5%">
                            {{$puntaje}}
                        </td>
                        <td style="text-align: center;width: 15%">
                            <strong> {{ number_format($totalPlan, 2) }}</strong>
                        </td>
                    </tr>
                    @if($puntajePlan && $puntajePlan->justificacion)
                        <tr>
                            <td style="text-align: left;background-color: #e1e1e1;word-wrap: break-word; word-break: break-word; white-space: normal;" colspan="4"><strong>Justificación:</strong> {!!  $puntajePlan->justificacion !!}</td>
                        </tr>
                    @endif
                    @php
                        //$totalGrupo += $totalPlan;

                        $subPuntaje += $totalPlan;

                    @endphp

                @endforeach


                @php
                    $total += $subPuntaje;

                @endphp
            @endif
    </table>

            <!--<div class="page-break"></div>-->
    <table>



        @php
            //$total += $totalGrupo;

        @endphp

        @php

            $totalGrupo=0;
            $subPuntaje=0;
            $totalItem=0;
            $submax = 0;
            $sub = 0;
            $j = 0;

           $primereventos = $eventoMaximos->first();
           $cantJustificaciones=0;

        @endphp
        @foreach($eventoMaximos as $eventoMaximo)//lo recorro 2 veces para saber cuales eventos tienen cargados justificación
            @php
                $puntajeEvento = $evaluacion->puntaje_eventos->where('viaje_evaluacion_planilla_evento_max_id', $eventoMaximo->id)->first();

                if ($puntajeEvento && $puntajeEvento->justificacion) {
                        $cantJustificaciones++;
                }
            @endphp

        @endforeach

        @foreach($eventoMaximos as $eventoMaximo)

                @if ($submax != $eventoMaximo->evaluacion_grupo_id)
                    @if ($loop->index != 0 && $eventoMaximos[$loop->index - 1]->grupo_maximo != 0)
                        @php
                            $subPuntaje = ($subPuntaje>$eventoMaximos[$loop->index - 1]->grupo_maximo)?$eventoMaximos[$loop->index - 1]->grupo_maximo:$subPuntaje;
                        @endphp
                        <td colspan="3" style="text-align: right;background-color: #e1e1e1;width: 15%">Subtotal (max. {{ $eventoMaximos[$loop->index - 1]->grupo_maximo }} )</td><td style="text-align: center;background-color: #e1e1e1;"><strong>{{ number_format($subPuntaje, 2)}} </strong></td>

                    @endif
                    @php
                        $submax = $eventoMaximo->evaluacion_grupo_id;
                    @endphp
                @endif
                @if ($sub != $eventoMaximo->evaluacion_grupo_id && $eventoMaximo->evaluacion_grupo_id)
                    @php
                        $j++;
                        $totalGrupo += $subPuntaje;
                        $subPuntaje=0;
                    @endphp

                    @php
                        $sub = $eventoMaximo->evaluacion_grupo_id;
                    @endphp

                @endif
                @php
                    $puntajeEvento = $evaluacion->puntaje_eventos->where('viaje_evaluacion_planilla_evento_max_id', $eventoMaximo->id)->first();
                    $puntaje = (($puntajeEvento)&&($puntajeEvento->puntaje))?(int)$puntajeEvento->puntaje:'';

                    $c_u=' c/u';

                    $hasta = (($eventoMaximo->maximo!=0)&&($eventoMaximo->minimo==$eventoMaximo->maximo))?((($eventoMaximo->minimo==$eventoMaximo->maximo))?$eventoMaximo->maximo. ' pt.':$eventoMaximo->maximo. $c_u):(($eventoMaximo->minimo!=$eventoMaximo->maximo)?'Hasta '.$eventoMaximo->maximo:'Hasta '.$eventoMaximo->tope);

                    //$hasta = $eventoMaximo->maximo. ' c/u';

                    $totalEvento = ($puntajeEvento)?$puntaje:0;

                    $descripcion = str_replace('#puntaje#', '<B>'.$eventoMaximo->maximo.' puntos</B>',$eventoMaximo->evento_nombre);
                @endphp
                <tr>
                    @if($primereventos->id == $eventoMaximo->id)
                        <td style="text-align: center;background-color: #e1e1e1;" rowspan="{{($eventoMaximos->count()+1+$cantJustificaciones)}}"><strong>Max. {{$eventoMaximo->grupo_maximo}}pt.</strong></td>
                    @endif

                    <td style="text-align: left;width: 50%">{!!  $descripcion !!}</td>
                    <td style="text-align: right; ">
                        {!! $hasta !!}
                    </td>
                    <td style="text-align: right;width: 5%">
                        {{$puntaje}}
                    </td>
                    <td style="text-align: center;width: 15%">
                        <strong> {{ number_format($totalEvento, 2) }}</strong>
                    </td>
                </tr>
                @if($puntajeEvento && $puntajeEvento->justificacion)
                        <tr>
                            <td style="text-align: left;background-color: #e1e1e1;word-wrap: break-word; word-break: break-word; white-space: normal;" colspan="4"><strong>Justificación:</strong> {!!  $puntajeEvento->justificacion !!}</td>
                        </tr>
                @endif
                @php
                    //$totalGrupo += $totalEvento;

                    $subPuntaje += $totalEvento;

                @endphp

        @endforeach
        @php

            $ultimoEventoMaximo=$eventoMaximos->last();
            //dd($ultimoAntAcadMaximo);
        @endphp
        @if($ultimoEventoMaximo->grupo_maximo!=0)
            @php
                $subPuntaje = ($subPuntaje>$ultimoEventoMaximo->grupo_maximo)?$ultimoEventoMaximo->grupo_maximo:$subPuntaje;
            @endphp
            <tr>
                <td colspan="3" style="text-align: right;background-color: #e1e1e1;"> Subtotal (max. {{$ultimoEventoMaximo->grupo_maximo}})</td><td style="text-align: center;background-color: #e1e1e1;"><strong>{{ number_format($subPuntaje, 2) }}</strong></td>
            </tr>
        @endif

        @php
            $total += $subPuntaje;

        @endphp
    <tr>
        <td colspan="4" style="text-align: right;"><strong> Total (max. <span>{{$planilla->maximo}}</span> )</strong></td><td style="text-align: center;background-color: #e1e1e1;"><strong>{{ number_format($total, 2) }}</strong></td>
    </tr>
</table>

</div>
<div class="content" style="margin-top: 50px;">
    <div style="background-color: #000;color:#FFF">Observaciones</div>
</div>
<div class="content">
    <span style="display: inline-block; border: 1px solid #ccc;width: 695px; padding-top: {{ $evaluacion->observaciones ? '0' : '15px' }};">{{ $evaluacion->observaciones }}</span>
</div>

<div class="signature-aclaracion">
    <div class="signature-no-line"></div>
    <div class="signature-no-line">{{$evaluador}}</div>
</div>

<div class="signature">
    <div class="signature-line">Firma</div>
    <div class="signature-line">Aclaración</div>
</div>

</body>
</html>
