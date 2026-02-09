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
    case 'C) Estadía de Trabajo en la UNLP para un Investigador Invitado':
        $motivoLetra='C';
        break;

}

?>
<div class="header">
    <div class="logo">

        <img src="{{ public_path('/images/unlp.png') }}" alt="UNLP Logo" class="logo">

    </div>
    <div class="text-content">
        <div class="title">
            SOLICITUD DE SUBSIDIOS {{ $year }}
        </div>
        <div class="subtitle">
            Viajes/Estadías
        </div>
        <div class="subtitle">
            {{$mes_desde}} {{ $year }} - {{$mes_hasta}} {{ intval($year)+1 }}
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
@if(intval($year)>2018)
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 200px;">Link del perfil google scholar</span> <span style="display: inline-block; border: 1px solid #ccc;width: 495px; padding-top: {{ $scholar ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $scholar }}</span>

    </div>

</div>
@endif
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Título de grado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 350px; padding-top: {{ $titulo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $titulo }}</span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 70px;">F. Egreso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $egreso ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($egreso)?date('d/m/Y', strtotime($egreso)):'' }}</span>
    </div>

</div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 230px;">Lugar de Trabajo de Inv. en la UNLP</span> <span style="display: inline-block; border: 1px solid #ccc;width: 465px; padding-top: {{ $unidad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $unidad }}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 70px;">Dirección</span> <span style="display: inline-block; border: 1px solid #ccc;width: 400px; padding-top: {{ $direccion_unidad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $direccion_unidad }}</span><span style="display: inline-block;width: 35px;"></span>
        <span style="display: inline-block;width: 60px;">Teléfono</span> <span style="display: inline-block; border: 1px solid #ccc;width: 120px; padding-top: {{ $telefono_unidad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $telefono_unidad }}</span>
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
@if(!empty($beca_beca))
    <div class="content">
        <div>BECARIO</div>
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 70px;">Institución</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: {{ $beca_institucion ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($beca_unlp)?'Universidad Nacional de La Plata':$beca_institucion }}</span>

        </div>

    </div>
    <div class="content">
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 100px;">Nivel de beca</span> <span style="display: inline-block; border: 1px solid #ccc;width: 230px; padding-top: {{ $beca_beca ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $beca_beca }}</span><span style="display: inline-block;width: 105px;"></span>
            <span style="display: inline-block;width: 50px;">Período</span> <span style="display: inline-block; border: 1px solid #ccc;width: 200px; padding-top: {{ $beca_periodo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $beca_periodo}}</span>
        </div>

    </div>
    <div class="content">
        <div class="content" style="margin-top: 10px;">
            <span style="display: inline-block;width: 130px;">Lugar de Trabajo</span> <span style="display: inline-block; border: 1px solid #ccc;width: 565px; padding-top: {{ $unidadbeca ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $unidadbeca }}</span>

        </div>

    </div>
    @if(intval($year)>2017)
        <div class="content">
            <div>RESUMEN DE LA BECA</div>
        </div>

        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $resumen_beca ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $resumen_beca)) !!}</span>
        </div>
    @endif
@endif
<div class="content">

    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100px;">Categoría SPU</span> <span style="display: inline-block; border: 1px solid #ccc;width: 50px; padding-top: {{ $categoria ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $categoria }}</span>
        <span style="display: inline-block;width: 120px;">Categoría SICADI</span> <span style="display: inline-block; border: 1px solid #ccc;width: 50px; padding-top: {{ $sicadi ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $sicadi }}</span>
        <span style="display: inline-block;width: 20px;"></span>
        <span style="display: inline-block;width: 80px;">Postulante</span> <span style="display: inline-block; border: 1px solid #ccc;width: 250px; padding-top: {{ $tipo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $tipo }}</span>

    </div>

</div>

@if(!empty($proyectosActuales))
    @if(intval($year)>2016)
        <div class="content">
            <div>PROYECTO DE INVESTIGACION SELECCIONADO EN EL MARCO DEL CUAL SE REALIZARA LA ACTIVIDAD</div>


        </div>
        @foreach ($proyectosActuales as $proyectoActual)
            @if($proyectoActual['seleccionado'])
                <div class="content">

                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Título</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: {{ $proyectosActuales[0]['titulo'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['titulo'] }}</span>

                    </div>

                </div>
                <div class="content">
                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Director</span> <span style="display: inline-block; border: 1px solid #ccc;width: 400px; padding-top: {{ $proyectoActual['director'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['director'] }}</span><span style="display: inline-block;width: 35px;"></span>
                        <span style="display: inline-block;width: 60px;">Estado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 120px; padding-top: {{ $proyectoActual['estado'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['estado'] }}</span>
                    </div>

                </div>
                <div class="content">

                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Código</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $proyectoActual['codigo'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['codigo'] }}</span>
                        <span style="display: inline-block;width: 90px;"></span>
                        <span style="display: inline-block;width: 60px;">Desde</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $proyectoActual['inicio'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($proyectoActual['inicio']) ? date('d/m/Y', strtotime($proyectoActual['inicio'])) : '' }}</span>
                        <span style="display: inline-block;width: 85px;"></span>
                        <span style="display: inline-block;width: 60px;">Hasta</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $proyectoActual['fin'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{  ($proyectoActual['fin']) ? date('d/m/Y', strtotime($proyectoActual['fin'])) : '' }}</span>

                    </div>

                </div>
                <div class="content">
                    <div>Resumen</div>
                </div>

                <div class="content">
                    <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $proyectoActual['resumen'] ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $proyectoActual['resumen'])) !!}</span>
                </div>
            @endif
        @endforeach
            @if(count($proyectosActuales)>1)
                <div class="content">
                    <div>OTROS PROYECTOS EN LOS QUE PARTICIPA</div>


                </div>
            @endif
        @foreach ($proyectosActuales as $proyectoActual)
            @if(!$proyectoActual['seleccionado'])
                        <div class="content">

                            <div class="content" style="margin-top: 10px;">
                                <span style="display: inline-block;width: 70px;">Título</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: {{ $proyectosActuales[0]['titulo'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['titulo'] }}</span>

                            </div>

                        </div>
                        <div class="content">
                            <div class="content" style="margin-top: 10px;">
                                <span style="display: inline-block;width: 70px;">Director</span> <span style="display: inline-block; border: 1px solid #ccc;width: 400px; padding-top: {{ $proyectoActual['director'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['director'] }}</span><span style="display: inline-block;width: 35px;"></span>
                                <span style="display: inline-block;width: 60px;">Estado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 120px; padding-top: {{ $proyectoActual['estado'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['estado'] }}</span>
                            </div>

                        </div>
                        <div class="content">

                            <div class="content" style="margin-top: 10px;">
                                <span style="display: inline-block;width: 70px;">Código</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $proyectoActual['codigo'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['codigo'] }}</span>
                                <span style="display: inline-block;width: 90px;"></span>
                                <span style="display: inline-block;width: 60px;">Desde</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $proyectoActual['inicio'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($proyectoActual['inicio']) ? date('d/m/Y', strtotime($proyectoActual['inicio'])) : '' }}</span>
                                <span style="display: inline-block;width: 85px;"></span>
                                <span style="display: inline-block;width: 60px;">Hasta</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $proyectoActual['fin'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{  ($proyectoActual['fin']) ? date('d/m/Y', strtotime($proyectoActual['fin'])) : '' }}</span>

                            </div>

                        </div>
                        <div class="content">
                            <div>Resumen</div>
                        </div>

                        <div class="content">
                            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $proyectoActual['resumen'] ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $proyectoActual['resumen'])) !!}</span>
                        </div>

                @endif
        @endforeach
    @else
        <div class="content">
            <div>PROYECTO/S ACREDITADO/S EN EL/LOS QUE PARTICIPA ACTUALMENTE SELECCIONADO/S</div>


        </div>
        @foreach ($proyectosActuales as $proyectoActual)

                <div class="content">

                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Título</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: {{ $proyectosActuales[0]['titulo'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['titulo'] }}</span>

                    </div>

                </div>
                <div class="content">
                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Director</span> <span style="display: inline-block; border: 1px solid #ccc;width: 400px; padding-top: {{ $proyectoActual['director'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['director'] }}</span><span style="display: inline-block;width: 35px;"></span>
                        <span style="display: inline-block;width: 60px;">Estado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 120px; padding-top: {{ $proyectoActual['estado'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['estado'] }}</span>
                    </div>

                </div>
                <div class="content">

                    <div class="content" style="margin-top: 10px;">
                        <span style="display: inline-block;width: 70px;">Código</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $proyectoActual['codigo'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyectoActual['codigo'] }}</span>
                        <span style="display: inline-block;width: 90px;"></span>
                        <span style="display: inline-block;width: 60px;">Desde</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $proyectoActual['inicio'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($proyectoActual['inicio']) ? date('d/m/Y', strtotime($proyectoActual['inicio'])) : '' }}</span>
                        <span style="display: inline-block;width: 85px;"></span>
                        <span style="display: inline-block;width: 60px;">Hasta</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $proyectoActual['fin'] ? '0' : '15px' }}; background-color: #e1e1e1;">{{  ($proyectoActual['fin']) ? date('d/m/Y', strtotime($proyectoActual['fin'])) : '' }}</span>

                    </div>

                </div>
                <div class="content">
                    <div>Resumen</div>
                </div>

                <div class="content">
                    <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $proyectoActual['resumen'] ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $proyectoActual['resumen'])) !!}</span>
                </div>

        @endforeach

    @endif
@endif
@php
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
    @endphp
<div class="content">
    <div>{{$tituloAmbitos}}</div>
    <table>
        <tr style="background-color: #999999;">
            <th>Institución</th><th>Ciudad</th><th>País</th><th>Desde</th><th>Hasta</th>
        </tr>
        @foreach($ambitos as $ambito)
            <tr>
                <td>{{$ambito->institucion}}</td><td>{{$ambito->ciudad}}</td><td>{{$ambito->pais}}</td><td>{{ ($ambito->desde)?date('d/m/Y', strtotime($ambito->desde)):'' }}</td><td>{{ ($ambito->hasta)?date('d/m/Y', strtotime($ambito->hasta)):'' }}</td>
            </tr>
        @endforeach
    </table>
</div>
@if(intval($year)>2011)
    @if(intval($year)==2012)
        <div class="content">
            <div>OBJETIVOS DEL VIAJE - JUSTIFICACION Y RELACION DE LAS TAREAS A REALIZAR CON EL PROYECTO DE INVESTIGACION - RELEVANCIA INSTITUCIONAL</div>
        </div>
        <div class="content">
            <div>Si el motivo de la solicitud es A)Reuniones Científicas deberá aclarar si realiza otra actividad además de presentar su trabajo (por ej. coordinador/a, comentarista de ponencias, panelista, presentador/a de libros o alguna otra actividad)</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $objetivo ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $objetivo)) !!}</span>
        </div>
    @endif
@else
    <div class="content">
        <div>OBJETIVO DEL VIAJE Y RELACION DE LAS TAREAS A REALIZAR CON EL PROYECTO DE INVESTIGACION</div>
    </div>

    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $objetivo ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $objetivo)) !!}</span>
    </div>
@endif
<div class="content">
    <div class="content" style="margin-top: 10px;">
        @if(intval($year)<2026)
            <span style="display: inline-block;width: 300px;">MONTO SOLICITADO A LA UNLP (en pesos)</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $monto ? '0' : '15px' }}; background-color: #e1e1e1;">{{ '$' . number_format($monto, 2, ',', '.') }}</span>
        @else
           <span style="display: inline-block;width: 300px;">MONTO SOLICITADO A LA UNLP (en pesos)</span> <span style="display: inline-block; border: 1px solid #ccc;width: 395px; padding-top: {{ $monto ? '0' : '15px' }}; background-color: #e1e1e1;">{{$montosViajesLugares[intval($monto)] ?? ''}} - {{ '$' . number_format($monto, 2, ',', '.') }}</span>
        @endif
    </div>

</div>
<div class="content">
    <div>MONTO SOLICITADO A OTROS ORGANISMOS</div>
    <table>
        <tr style="background-color: #999999;">
            <th>Institución</th><th>Carácter</th><th>Importe</th>
        </tr>
        @foreach($montos as $monto)
            <tr>
                <td>{{$monto->institucion}}</td><td>{{$monto->ciudad}}</td><td>{{'$' . number_format($monto->monto, 2, ',', '.')}}</td>
            </tr>
        @endforeach
    </table>
</div>
<div class="content">
    <div style="background-color: #000000; color: #ffffff">{{$motivo}}</div>
</div>
@if ($motivoLetra == 'A')
    @if(intval($year)>2012)
        @if(intval($year)<2017)
            <div class="content">
                <div>OBJETIVOS DEL VIAJE - JUSTIFICACION Y RELACION DE LAS TAREAS A REALIZAR CON EL PROYECTO DE INVESTIGACION - RELEVANCIA INSTITUCIONAL</div>
            </div>
            <div class="content">
                <div>Deberá aclarar si realiza otra actividad además de la actividad motivo de esta solicitada (por ej. coordinador/a, comentarista de ponencias, panelista, presentador/a de libros o alguna otra actividad)</div>
            </div>
        @else
            <div class="content">
                <div>OBJETIVOS DEL VIAJE - JUSTIFICACION Y RELACION CON EL PROYECTO DE INVESTIGACION</div>
            </div>
        @endif
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $objetivo ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $objetivo)) !!}</span>
        </div>
        @if(intval($year)>2016)
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
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $relevanciaA)) !!}</span>
            </div>
        @endif
    @endif
    @if($congreso)
        @if(intval($year)<2023)
            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    @php
                        switch ($congreso) {
                        case 1:
                            $tipo_congreso = 'CONGRESO';
                            break;
                        case 2:
                            $tipo_congreso = 'CONFERENCIA';
                            break;
                    }
                        @endphp
                    <span style="display: inline-block;width: 70px;">Tipo</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: {{ $tipo_congreso ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $tipo_congreso }}</span>

                </div>

            </div>
        @endif
    @endif
    @if(intval($year)<2023)
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                @php
                    $labelCongreso = ($congreso==2)?'Título de la Conferencia':'Título del Trabajo';
                @endphp
                <span style="display: inline-block;width: 140px;">{{$labelCongreso}}</span> <span style="display: inline-block; border: 1px solid #ccc;width: 555px; padding-top: {{ $titulotrabajo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $titulotrabajo}}</span>

            </div>

        </div>
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                @php
                    $labelAutores = ($congreso==2)?'Autor':'Autores del Trabajo';
                @endphp
                <span style="display: inline-block;width: 140px;">{{$labelAutores}}</span> <span style="display: inline-block; border: 1px solid #ccc;width: 555px; padding-top: {{ $autores ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $autores}}</span>

            </div>

        </div>
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                @php
                    $labelNombre = ($congreso==2)?'Congreso donde se dictará la conferencia':'Nombre del Congreso';
                @endphp
                <span style="display: inline-block;width: 170px;">{{$labelNombre}}</span> <span style="display: inline-block; border: 1px solid #ccc;width: 525px; padding-top: {{ $congresonombre ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $congresonombre}}</span>

            </div>

        </div>
        @if($nacional)
            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    @php
                        switch ($nacional) {
                        case 1:
                            $tipo_nacional = 'NACIONAL';
                            break;
                        case 2:
                            $tipo_nacional = 'INTERNACIONAL';
                            break;
                    }
                    @endphp
                    <span style="display: inline-block;width: 70px;">Carácter</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: {{ $tipo_nacional ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $tipo_nacional }}</span>

                </div>

            </div>
        @endif
        @if(intval($year)>2016)
            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    <span style="display: inline-block;width: 60px;">Lugar</span> <span style="display: inline-block; border: 1px solid #ccc;width: 255px; padding-top: {{ $lugartrabajo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $lugartrabajo }}</span>
                    <span style="display: inline-block;width: 80px;">Fecha Inicio</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $trabajodesde ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($trabajodesde)?date('d/m/Y', strtotime($trabajodesde)):'' }}</span>
                    <span style="display: inline-block;width: 80px;">Fecha Fin</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $trabajohasta ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($trabajohasta)?date('d/m/Y', strtotime($trabajohasta)):'' }}</span>
                </div>
            </div>
        @else

            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    <span style="display: inline-block;width: 60px;">Lugar</span> <span style="display: inline-block; border: 1px solid #ccc;width: 405px; padding-top: {{ $lugartrabajo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $lugartrabajo }}</span>
                    <span style="display: inline-block;width: 80px;">Fecha Inicio</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $trabajodesde ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($trabajodesde)?date('d/m/Y', strtotime($trabajodesde)):'' }}</span>

                </div>
            </div>
        @endif
        @if(intval($year)>2016)
            <div class="content">
                <div>Relevancia del evento</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $relevancia)) !!}</span>
            </div>
        @endif
    @else
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                <span style="display: inline-block;width: 220px;">Nombre de la Reunión Científica</span> <span style="display: inline-block; border: 1px solid #ccc;width: 475px; padding-top: {{ $congresonombre ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $congresonombre }}</span>
            </div>
        </div>
        @if($nacional)
            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    @php
                        switch ($nacional) {
                        case 1:
                            $tipo_nacional = 'NACIONAL';
                            break;
                        case 2:
                            $tipo_nacional = 'INTERNACIONAL';
                            break;
                    }
                    @endphp
                    <span style="display: inline-block;width: 70px;">Carácter</span> <span style="display: inline-block; border: 1px solid #ccc;width: 625px; padding-top: {{ $tipo_nacional ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $tipo_nacional }}</span>

                </div>

            </div>
            <div class="content">
                <div class="content" style="margin-top: 10px;">
                    <span style="display: inline-block;width: 200px;">Link de la Reunión Científica</span> <span style="display: inline-block; border: 1px solid #ccc;width: 495px; padding-top: {{ $link ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $link }}</span>
                </div>
            </div>
        @endif
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                <span style="display: inline-block;width: 60px;">Lugar</span> <span style="display: inline-block; border: 1px solid #ccc;width: 255px; padding-top: {{ $lugartrabajo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $lugartrabajo }}</span>
                <span style="display: inline-block;width: 80px;">Fecha Inicio</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $trabajodesde ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($trabajodesde)?date('d/m/Y', strtotime($trabajodesde)):'' }}</span>
                <span style="display: inline-block;width: 80px;">Fecha Fin</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $trabajohasta ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($trabajohasta)?date('d/m/Y', strtotime($trabajohasta)):'' }}</span>
            </div>
        </div>


        <div class="content">
            <div>Relevancia del evento</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $relevancia)) !!}</span>
        </div>
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                @php
                    $labelCongreso = ($congreso==2)?'Título de la Conferencia':'Título del Trabajo';
                @endphp
                <span style="display: inline-block;width: 140px;">{{$labelCongreso}}</span> <span style="display: inline-block; border: 1px solid #ccc;width: 555px; padding-top: {{ $titulotrabajo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $titulotrabajo}}</span>

            </div>

        </div>
        <div class="content">
            <div class="content" style="margin-top: 10px;">
                @php
                    $labelAutores = ($congreso==2)?'Autor':'Autores del Trabajo';
                @endphp
                <span style="display: inline-block;width: 140px;">{{$labelAutores}}</span> <span style="display: inline-block; border: 1px solid #ccc;width: 555px; padding-top: {{ $autores ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $autores}}</span>

            </div>

        </div>



    @endif

    @php
        $labelResumen = ($congreso==2)?' de la Conferencia':' del Trabajo';
    @endphp

    <div class="content">
        <div>Resumen {{$labelResumen}}</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $resumen)) !!}</span>
    </div>
    @if(intval($year)>2016)
        <div class="content">
            <div class="content" style="margin-top: 10px;">

                <span style="display: inline-block;width: 200px;">Modalidad de la presentación</span> <span style="display: inline-block; border: 1px solid #ccc;width: 495px; padding-top: {{ $modalidad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $modalidad}}</span>

            </div>

        </div>
    @endif
@endif
@if ($motivoLetra == 'C')
    <div class="content">
        <div class="content" style="margin-top: 10px;">

            <span style="display: inline-block;width: 150px;">Profesor Visitante</span> <span style="display: inline-block; border: 1px solid #ccc;width: 445px; padding-top: {{ $profesor ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $profesor}}</span>

        </div>

    </div>
    <div class="content">
        <div class="content" style="margin-top: 10px;">

            <span style="display: inline-block;width: 200px;">Lugar de Origen del Prof. Visitante</span> <span style="display: inline-block; border: 1px solid #ccc;width: 495px; padding-top: {{ $lugarprofesor ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $lugarprofesor}}</span>

        </div>

    </div>
@endif
@if(intval($year)==2012)
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
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $libros)) !!}</span>
    </div>
    <div class="content">
        <div>LIBROS (COMPILACIONES)</div>
    </div>
    <div class="content">
        <div>Compilador - Título - Editor - Edición(Nacional/Internacional) - ISBN - Lugar de Publicación - Año</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $compilados)) !!}</span>
    </div>
    <div class="content">
        <div>CAPITULOS DE LIBROS</div>
    </div>
    <div class="content">
        <div>Autores - Capítulo/s - Título del Libro - Editor - Edición(Nacional/Internacional) - ISBN - Lugar de Publicación - Año</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $capitulos)) !!}</span>
    </div>
    @php
    $referato = (($tipo == 'Investigador Formado')&&($motivoLetra=='A'))?'con o sin referato':'solo con referato';
    $ponerReferato = (($tipo == 'Investigador En Formación')&&($motivoLetra=='A'))?'- Con Referato':'';
    @endphp
    <div class="content">
        <div>ARTICULOS EN REVISTAS ({{$referato}})</div>
    </div>
    <div class="content">
        <div>Autor/es - Título - Revista - ISSN - Volumen - Nro. - Páginas - Año {{$ponerReferato}} - Nacional o Internacional</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $articulos)) !!}</span>
    </div>
    <div class="content">
        <div>CONGRESOS (TRABAJOS COMPLETOS PUBLICADOS EN ACTAS CON REFERATO)</div>
    </div>
    <div class="content">
        <div>Autor/es - Título trabajo - Congreso - Lugar - Volumen - Nro. - Páginas - Año - Fecha - Carácter(Nacional/Internacional)</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $congresos)) !!}</span>
    </div>
    <div class="content">
        <div>PATENTES</div>
    </div>
    <div class="content">
        <div>Autor/es - Título - Código de Patente - Año</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $patentes)) !!}</span>
    </div>
    <div class="content">
        <div>REGISTROS DE PROPIEDAD INTELECTUAL</div>
    </div>
    <div class="content">
        <div>Tipo - Título - Titular/es - Registro Nro. - País - Autor/es</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $intelectuales)) !!}</span>
    </div>
    <div class="content">
        <div>INFORMES TECNICOS</div>
    </div>
    <div class="content">
        <div>Autor/es - Título - Año - Institución</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $informes)) !!}</span>
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
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $tesis)) !!}</span>
    </div>
    <div class="content">
        <div>DIR./CODIR. BECAS DE POSGRADO / DIR./CODIR. TESIS DE POSGRADO EN REALIZACION</div>
    </div>
    <div class="content">
        <div>Año - Apellido y Nombre - Tema - Universidad - (Dir./Codir.) - Si es Tesis (Doctorado/Maestría) - Si es Beca (Tipo de Beca)</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $becas)) !!}</span>
    </div>
    <div class="content">
        <div>DIR./CODIR. TESINAS DE GRADO / DIR./CODIR. BECAS DE ENTRENAMIENTO</div>
    </div>
    <div class="content">
        <div>Año - Apellido y Nombre - Tema - Universidad - (Dir./Codir.) - (Tesina de Grado/Beca de Entrenamiento)</div>
    </div>
    <div class="content">
        <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $tesinas)) !!}</span>
    </div>
@endif
@if(intval($year)>2012)
    @if($motivoLetra=='B')
        <div class="content">
            <div style="font-weight: bold">PLAN DE TRABAJO DE INVESTIGACIÓN (para los tipo B)</div>
        </div>
        <div class="content">
            <div>1. Objetivo general de la estadía</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $generalB)) !!}</span>
        </div>
        <div class="content">
            <div>2. Objetivos específicos de la estadía</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $especificoB)) !!}</span>
        </div>
        @if(intval($year)>2016)
            <div class="content">
                <div>3. Plan de trabajo de investigación a realizar en el período</div>
            </div>
        @else
            <div class="content">
                <div>3. Detalle de las actividades de invest. a realizar en el período relacionado con el proy. de invest. en el que participa</div>
            </div>
        @endif
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $actividadesB)) !!}</span>
        </div>
        <div class="content">
            <div>4.- Cronograma</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $cronogramaB)) !!}</span>
        </div>
        @if(intval($year)>2017)
            <div class="content">
                <div>5.- Justificación de la realización de la estadía y relación con el proyecto de investigación en el que participa</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $justificacionB)) !!}</span>
            </div>
        @endif
        @if(intval($year)>2016)
            @if(intval($year)>2017)
                <div class="content">
                    <div>6. Relevancia institucional</div>
                    <ul>
                        <li>La afinidad y los aportes del grupo receptor a la línea de investigación del solicitante</li>
                        <li>La correspondencia del plan de trabajo a realizar con la línea de investigación del solicitante así como su factibilidad</li>
                        <li>Los aportes del desarrollo del plan de trabajo a la línea de investigación del solicitante</li>
                        <li>La transferencia que realizará el solicitante a su equipo de investigación, Unidad de Investigación y/o Unidad Académica a partir de la realización de su estadía</li>
                    </ul>
                </div>
            @else
                <div class="content">
                    <div>5.- Relevancia institucional: (Detalle de las actividades de investigación a realizar relacionadas con el proyecto de investigación en el que participa, la afinidad y los aportes del grupo receptor a la línea de investigación del solicitante, la transferencia que realizará el solicitante a su equipo de investigación, a la unidad de investigación y a la unidad académica</div>
                </div>
            @endif
        @else
            <div class="content">
                <div>5.- Aportes al grupo de investigación</div>
            </div>
        @endif
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $aportesB)) !!}</span>
        </div>
        @if(intval($year)>2016)
            @if(intval($year)>2017)
                <div class="content">
                    <div>7. Relevancia del lugar donde realiza la estadía. Justifique la elección del lugar</div>
                </div>
            @else
                <div class="content">
                    <div>6. Relevancia del lugar donde realiza la estadía. Justifique la elección del lugar</div>
                </div>
            @endif
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $relevanciaB)) !!}</span>
            </div>
        @endif
    @endif
    @if($motivoLetra=='C')
        <div class="content">
            <div style="font-weight: bold">PLAN DE TRABAJO DE INVESTIGACIÓN (para los tipo C)</div>
        </div>
        <div class="content">
            <div>1. Objetivo general de la estadía</div>
        </div>
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $objetivosC)) !!}</span>
        </div>
        @if(intval($year)>2016)
            <div class="content">
                <div>2. Plan de trabajo de investigación a realizar en el período</div>
            </div>
        @else
            <div class="content">
                <div>2. Plan de actividades de invest. a realizar en el período, en relación con el proy. de investigación del grupo receptor</div>
            </div>
        @endif
        <div class="content">
            <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $planC)) !!}</span>
        </div>
        @if(intval($year)>2016)
            <div class="content">
                <div>3. Relación del plan de trabajo del investigador invitado con el proyecto de investigación acreditado del grupo receptor</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $relacionProyectoC)) !!}</span>
            </div>
        @else
            <div class="content">
                <div>3. Aportes del desarrollo del plan de trabajo al grupo de investigación, Unidad de Investigación y/o Unidad Académica</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $aportesC)) !!}</span>
            </div>
        @endif
        @if(intval($year)>2016)
            <div class="content">
                <div>4. Aportes del desarrollo del plan de trabajo al grupo de investigación, Unidad de Investigación y/o Unidad Académica</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $aportesC)) !!}</span>
            </div>
        @else
            <div class="content">
                <div>4. Otras actividades (ejemplo: dictado de cursos, seminarios, participación en eventos científicos, etc.</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $actividadesC)) !!}</span>
            </div>
        @endif
        @if(intval($year)>2016)
            <div class="content">
                <div>5. Otras actividades</div>
            </div>
            <div class="content">
                <span style="display: inline-block; border: 1px solid #ccc;width: 700px; padding-top: {{ $relevanciaA ? '0' : '15px' }}; background-color: #e1e1e1; white-space: pre-wrap;">{!! nl2br(e( $actividadesC)) !!}</span>
            </div>
        @endif
    @endif
@endif



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
    <div style="text-align: center;">AVAL DE LA {{ $facultadplanilla }}</div>
</div>
<div class="signature">
    <div class="signature-line">Lugar y Fecha </div>
    <div class="signature-line">Firma del Decano</div>
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
@if(intval($year)<2026)
    <div class="content">
        <div style="text-align: center"><strong>PRESUPUESTO ESTIMADO PRELIMINAR</strong></div>
    </div>
@endif
<div class="content">
    <div>{{$tituloAmbitos}}</div>
    <table>
        <tr style="background-color: #999999;">
            <th>Institución</th><th>Ciudad</th><th>País</th><th>Desde</th><th>Hasta</th>
        </tr>
        @foreach($ambitos as $ambito)
            <tr>
                <td>{{$ambito->institucion}}</td><td>{{$ambito->ciudad}}</td><td>{{$ambito->pais}}</td><td>{{ ($ambito->desde)?date('d/m/Y', strtotime($ambito->desde)):'' }}</td><td>{{ ($ambito->hasta)?date('d/m/Y', strtotime($ambito->hasta)):'' }}</td>
            </tr>
        @endforeach
    </table>
</div>
@php
    $total = 0;
@endphp
@foreach ($tipoPresupuestos as $tipoPresupuesto)

    <div class="content">
        @if(intval($year)<2026)
            <div>{{$tipoPresupuesto->nombre}}</div>
        @else
            <div>PRESUPUESTO ESTIMADO PRELIMINAR</div>
            <div style="font-size: 10px;">En caso de ser adjudicado el subsidio, el monto máximo será el indicado en las pautas del llamado en el ítem “montos a asignar” según el lugar donde realizará la actividad</div>
        @endif
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

                                Viáticos - Días: {{$detalles[1] ?? '' }} - Lugar: {{$detalles[2] ?? ''}}
                            @elseif($concepto === 'Pasajes')

                                Pasajes - {{ $detalles[1] ?? '' }} - Destino: {{ $detalles[2] ?? '' }}
                            @elseif($concepto === 'Alojamiento')

                                Alojamiento - Noches: {{$detalles[1] ?? ''}} - Lugar: {{$detalles[2] ?? ''}}
                            @elseif($concepto === 'Inscripcion')

                                Inscripción - Descripción: {{$detalles[1] ?? ''}}
                            @elseif($concepto === 'Otros')
                                Otros - Descripción: {{$detalles[1] ?? ''}}
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
            <!--<tr style="background-color: #e1e1e1;">
                <td colspan="3" style="text-align: right;">SUBTOTAL: {{ '$' . number_format($subtotal, 2, ',', '.') }}</td>
            </tr>-->
        </table>
    </div>
@endforeach

<table>
    <tr style="background-color: #e1e1e1;">
        <td colspan="3" style="text-align: right;">TOTAL: {{ '$' . number_format($total, 2, ',', '.') }}</td>
    </tr>
</table>





<div class="signature">
    <div class="signature-line">Lugar y Fecha</div>
    <div class="signature-line">Firma y Aclaración</div>
</div>



</body>
</html>
