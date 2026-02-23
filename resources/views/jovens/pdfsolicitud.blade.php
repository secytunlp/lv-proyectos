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

        <img src="{{ public_path('/images/unlp.png') }}" alt="UNLP Logo" class="logo">

    </div>
    <div class="text-content">
        <div class="title">
            SOLICITUD DE SUBSIDIOS {{ $year }}
        </div>
        <div class="subtitle">
            Jóvenes Investigadores de la UNLP
        </div>
    </div>
</div>



<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Apellido y Nombres</span> <span style="display: inline-block; border: 1px solid #ccc;width: 350px; padding-top: {{ $solicitante ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $solicitante }}</span><span style="display: inline-block;width: 55px;"></span>
        <span style="display: inline-block;width: 40px;">CUIL</span> <span style="display: inline-block; border: 1px solid #ccc;width: 110px; padding-top: {{ $cuil ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $cuil }}</span>
    </div>

</div>

<div class="content">
    <div>Domicilio de notificación (Dentro del Radio Urbano de La Plata, Art. 20 Ord. 101)</div>
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 60px;">Calle</span> <span style="display: inline-block; border: 1px solid #ccc;width: 255px; padding-top: {{ $calle ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $calle }}</span>
        <span style="display: inline-block;width: 35px;">Nro.</span> <span style="display: inline-block; border: 1px solid #ccc;width: 45px; padding-top: {{ $nro ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $nro }}</span>
        <span style="display: inline-block;width: 35px;">Piso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 45px; padding-top: {{ $piso ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $piso }}</span>
        <span style="display: inline-block;width: 40px;">Dpto.</span> <span style="display: inline-block; border: 1px solid #ccc;width: 45px; padding-top: {{ $depto ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $depto }}</span>
        <span style="display: inline-block;width: 35px;">C.P.</span> <span style="display: inline-block; border: 1px solid #ccc;width: 60px; padding-top: {{ $cp ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $cp }}</span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 50px;">E-mail</span> <span style="display: inline-block; border: 1px solid #ccc;width: 420px; padding-top: {{ $email ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $email }}</span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 60px;">Teléfono</span> <span style="display: inline-block; border: 1px solid #ccc;width: 120px; padding-top: {{ $telefono ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $telefono }}</span>
    </div>
    @if(intval($year)>2012)
    <div style="font-size: 10px;">Acepto recibir toda notificación relativa a la presente solicitud en la dirección de correo electrónico declarada
        precedentemente {{ $notificacion }}</div>
    @endif
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Título de grado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 350px; padding-top: {{ $titulo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $titulo }}</span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 70px;">F. Egreso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $egreso ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($egreso)?date('d/m/Y', strtotime($egreso)):'' }}</span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Título de posgrado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 350px; padding-top: {{ $tituloposgrado ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $tituloposgrado }}</span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 70px;">F. Egreso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $egresoposgrado ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($egresoposgrado)?date('d/m/Y', strtotime($egresoposgrado)):'' }}</span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 230px;">Lugar de Trabajo de Inv. en la UNLP</span> <span style="display: inline-block; border: 1px solid #ccc;width: 465px; padding-top: {{ $unidad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $unidad }}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 150px;">Cargo docente UNLP</span> <span style="display: inline-block; border: 1px solid #ccc;width: 300px; padding-top: {{ $cargo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $cargo }}</span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 70px;">Dedicación</span> <span style="display: inline-block; border: 1px solid #ccc;width: 130px; padding-top: {{ $dedicacion ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $dedicacion}}</span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 70px;">Facultad</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: {{ $facultad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $facultad }}</span>

    </div>

</div>
@if(!empty($carrerainv))
    <div class="content">
        <div>INVESTIGADOR DE CARRERA</div>
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 70px;">Institución</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: {{ $organismo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $organismo }}</span>

        </div>

    </div>
    <div class="content">
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 100px;">Categoría</span> <span style="display: inline-block; border: 1px solid #ccc;width: 230px; padding-top: {{ $carrerainv ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $carrerainv }}</span><span style="display: inline-block;width: 105px;"></span>
            <span style="display: inline-block;width: 50px;">Ingreso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 200px; padding-top: {{ $ingreso_carrera ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($ingreso_carrera)?date('d/m/Y', strtotime($ingreso_carrera)):'' }} </span>
        </div>

    </div>
    <div class="content">
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 130px;">Lugar de Trabajo</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: {{ $unidadcarrera ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $unidadcarrera }}</span>

        </div>

    </div>
@endif
@if(!empty($beca))
<div class="content">
    <div>BECARIO</div>
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 70px;">Institución</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: {{ $beca->institucion ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $beca->institucion }}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100px;">Nivel de beca</span> <span style="display: inline-block; border: 1px solid #ccc;width: 230px; padding-top: {{ $beca->beca ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $beca->beca }}</span><span style="display: inline-block;width: 105px;"></span>
        <span style="display: inline-block;width: 50px;">Período</span> <span style="display: inline-block; border: 1px solid #ccc;width: 200px; padding-top: {{ $beca->desde ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($beca->desde)?date('d/m/Y', strtotime($beca->desde)):'' }} - {{ ($beca->hasta)?date('d/m/Y', strtotime($beca->hasta)):'' }}</span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Lugar de Trabajo</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: {{ $unidadbeca ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $unidadbeca }}</span>

    </div>

</div>
@endif
@if(!empty($becas))
    <div class="content">
        <div>BECAS ANTERIORES</div>

        <table>
            <tr style="background-color: #999999;">
                <th>Nivel beca</th><th>UNLP</th><th>F. desde</th><th>F. hasta</th>
            </tr>
            @foreach($becas as $becaAnt)
                <tr @if($becaAnt->agregada)
                        style="background-color: #CCCCCC;"
                    @endif>
                    <td>{{$becaAnt->beca}} - {{$becaAnt->institucion}}</td><td>{{($becaAnt->unlp)?'SI':'NO'}}</td><td>{{ ($becaAnt->desde)?date('d/m/Y', strtotime($becaAnt->desde)):'' }}</td><td>{{ ($becaAnt->hasta)?date('d/m/Y', strtotime($becaAnt->hasta)):'' }}</td>
                </tr>
            @endforeach
        </table>
    </div>

@endif
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 350px;">Es o ha sido DIR./CODIR. de proyectos de acreditación</span> <span style="display: inline-block; border: 1px solid #ccc;width: 345px; padding-top: {{ $director ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $director }}</span>

    </div>

</div>
@if(!empty($proyectosActuales))
    <div class="content">
        <div>PROYECTO/S ACREDITADO/S EN EL/LOS QUE PARTICIPA ACTUALMENTE</div>

        <table>
            <tr style="background-color: #999999;">
                <th>Código</th><th>Título</th><th>Director</th><th>Inicio</th><th>Fin</th>
            </tr>
            @foreach($proyectosActuales as $proyectoActual)
                <tr>
                    <td>{{$proyectoActual->codigo}}</td><td>{{$proyectoActual->titulo}}</td><td>{{$proyectoActual->director}}</td><td>{{ ($proyectoActual->desde)?date('d/m/Y', strtotime($proyectoActual->desde)):'' }}</td><td>{{ ($proyectoActual->hasta)?date('d/m/Y', strtotime($proyectoActual->hasta)):'' }}</td>
                </tr>
            @endforeach
        </table>
    </div>

@endif
@if(!empty($proyectosAnteriores))
    <div class="content">
        <div>PROYECTO/S ACREDITADO/S EN EL/LOS QUE PARTICIPO ANTERIORMENTE</div>

        <table>
            <tr style="background-color: #999999;">
                <th>Código</th><th>Título</th><th>Director</th><th>Inicio</th><th>Fin</th>
            </tr>
            @foreach($proyectosAnteriores as $proyectoAnterior)
                <tr
                @if($proyectoAnterior->agregado)
                    style="background-color: #e1e1e1;"
                @endif
                >
                    <td>{{$proyectoAnterior->codigo}}</td><td>{{$proyectoAnterior->titulo}}</td><td>{{$proyectoAnterior->director}}</td><td>{{ ($proyectoAnterior->desde)?date('d/m/Y', strtotime($proyectoAnterior->desde)):'' }}</td><td>{{ ($proyectoAnterior->hasta)?date('d/m/Y', strtotime($proyectoAnterior->hasta)):'' }}</td>
                </tr>
            @endforeach
        </table>
    </div>

@endif
<div class="page-break"></div> <!-- Esto genera el salto de página -->
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Apellido y Nombres</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: {{ $solicitante ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $solicitante }}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Facultad</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: {{ $facultadplanilla ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $facultadplanilla }}</span>

    </div>

</div>
@if(intval($year)>2012)
<div class="content">
    <div>Breve descripción de las actividades de I/D que plantea en el marco del proyecto en que se desempeña el solicitante</div>
</div>
<div class="content">
        <span style="display: inline-block; border: 1px solid #ccc; width: 695px; padding-top: {{ $objetivo ? '0' : '15px' }}; background-color: #e1e1e1;  white-space: pre-wrap;">
            {!! nl2br(e($objetivo)) !!}
        </span>
</div>
@if(intval($year)>2016)
<div class="content">
    <div>Justificar el pedido de fondos detallado en el presupuesto preliminar. Además, para cada ítem que solicita en el
        presupuesto preliminar deberá a) detallar el mismo y b) justificar su pedido. En el caso de solicitar bibliografía deberá
        indicar título, autor, editorial, etc.</div>
</div>
@else
    <div class="content">
        <div>Justificar el pedido de fondos detallado en el presupuesto preliminar</div>
    </div>
@endif
<div class="content">
    <span style="display: inline-block; border: 1px solid #ccc;width: 695px; padding-top: {{ $justificacion ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;word-break: break-word;
                 overflow-wrap: break-word;">{!! nl2br(e( $justificacion)) !!}</span>
</div>
@else
    <div class="content">
        <div>Breve descripción de las actividades de I/D que plantea en el marco del proyecto en que se desempeña el solicitante</div>
    </div>
    <div class="content">
    <span style="display: inline-block; border: 1px solid #ccc; width: 695px; padding-top: {{ $objetivo ? '0' : '15px' }}; background-color: #e1e1e1;  white-space: pre-wrap;word-break: break-word;
                 overflow-wrap: break-word;">
        {!! nl2br(e($objetivo)) !!}
    </span>

    </div>
    </div>
@endif
<div class="content">
    <div>Declaración Jurada <strong>(Sólo en caso de haber sido adjudicatario de subsidios anteriores)</strong></div>
</div>
<div class="content">
    @php
        $yearAnt = intval($year)-2;
    @endphp
    <div>Declaro que al momento de la presentación de la solicitud de subsidios {{$year}}, he entregado en la Secretaría
        de Ciencia y Técnica de la Universidad Nacional de La Plata el informe y constancia de la rendición
        efectuada en mi Unidad Académica correspondiente al subsidio OTORGADO EN EL PERIODO {{$year}} al
        {{$yearAnt}} inclusive. Tomo conocimiento que el no cumplimiento de lo mencionado precedentemente es motivo de
        exclusión de esta presentación.</div>
</div>

<div class="signature">

    <div class="signature-line">Firma</div>
</div>
<div class="page-break"></div> <!-- Esto genera el salto de página -->
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Apellido y Nombres</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: {{ $solicitante ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $solicitante }}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Facultad</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: {{ $facultadplanilla ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $facultadplanilla }}</span>

    </div>

</div>



<div class="content">
    <div><strong>Indicar y describir la aplicación del subsidio en caso que le sea otorgado. La descripcion deberá ser lo mas
            detallada y precisa posible.</strong></div>
</div>
<div class="content">
    <div style="text-align: center"><strong>PRESUPUESTO ESTIMADO PRELIMINAR</strong></div>
</div>
@php
    $total = 0;
@endphp
@foreach ($tipoPresupuestos as $tipoPresupuesto)

    <div class="content">
        <div>{{$tipoPresupuesto->nombre}}</div>

        <table>
            <tr style="background-color: #999999;">
                <th>FECHA</th><th>DESCRIPCION / CONCEPTO</th><th>Importe</th>
            </tr>
            @php
                $subtotal = 0;
            @endphp
            @foreach ($presupuestos->where('tipo_presupuesto_id', $tipoPresupuesto->id) as $presupuesto)
                <tr>
                    <td>{{ ($presupuesto->fecha) ? date('d/m/Y', strtotime($presupuesto->fecha)) : '' }}</td>
                    <td>
                        @if($tipoPresupuesto->id == 2)
                            @php
                            $detalles = explode('|', $presupuesto->detalle);
                            $concepto = $detalles[0];
                            @endphp
                            @if($concepto === 'Viaticos')

                                Viáticos - Días: {{$detalles[1]}} - Lugar: {{$detalles[2]}}
                            @elseif($concepto === 'Pasajes')

                                Pasajes - {{$detalles[1]}} - Destino: {{$detalles[2]}}
                            @elseif($concepto === 'Alojamiento')

                                Alojamiento - Noches: {{$detalles[1]}} - Lugar: {{$detalles[2]}}
                            @elseif($concepto === 'Inscripcion')

                                Inscripción - Descripción: {{$detalles[1]}}
                            @elseif($concepto === 'Otros')
                                Otros - Descripción: {{$detalles[1]}}
                            @endif
                        @else

                        {{ $presupuesto->detalle }}
                        @endif
                    </td>
                    <td style="text-align: right;">{{ '$' . number_format($presupuesto->monto, 2, ',', '.') }}</td>
                </tr>
                @php
                    $subtotal += $presupuesto->monto;
                    $total += $presupuesto->monto;
                @endphp
            @endforeach
            <tr style="background-color: #e1e1e1;">
                <td colspan="3" style="text-align: right;">SUBTOTAL: {{ '$' . number_format($subtotal, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>
@endforeach

<table>
    <tr style="background-color: #e1e1e1;">
        <td colspan="3" style="text-align: right;">TOTAL: {{ '$' . number_format($total, 2, ',', '.') }}</td>
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
