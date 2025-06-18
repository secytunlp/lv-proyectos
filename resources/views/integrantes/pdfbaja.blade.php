<!DOCTYPE html>
<html>
<head>
    <title>Baja de integrante</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 14px;}
        .header { text-align: center; font-weight: bold; }
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
        .content-border-full {
            border: 1px solid #ccc; /* Borde alrededor del texto */

            /*margin-top: 10px;*/
            width: 100%; /* Extender el span al ancho completo del contenedor */
            display: inline-block; /* Para que el borde se ajuste al contenido */

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
    </style>
</head>
<body>
<?php if ($estado === 'Baja Creada'): ?>
    <div class="watermark">VISTA_PRELIMINAR</div>
<?php endif; ?>
<div class="header">
    <img src="{{ url('/images/secyt.gif') }}"><br>
    <div class="unit-title">
        <span>BAJA DE {{$tipoIntegrante}} - B</span>
        01/{{ date('Y', strtotime($fecha)) }} GESTIÓN DE PROYECTOS
    </div>
</div>
<div class="content">
    <strong>UNIDAD ACADEMICA</strong> <span class="content-border" style="padding-top: {{ $facultad ? '0' : '15px' }}">{{ $facultad }}</span>
</div>

<div class="section-title">IDENTIFICACION DEL PROYECTO</div>
<div class="content" style="margin-top: 10px;">
    <span style="display: inline-block;width: 80px;"><strong>CODIGO</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 80px;">{{ $codigo }}</span> <span style="display: inline-block;width: 100px;"></span><span style="display: inline-block;width: 100px;"><strong>DURACION</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 325px; padding-top: {{ $duracion ? '0' : '15px' }} ">{{ $duracion }}</span>
</div>
<div class="content" style="margin-top: 10px;">
    <span style="display: inline-block;width: 240px;"><strong>DENOMINACION DEL PROYECTO</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 455px; font-size: 10px; padding-top: {{ $denominacion ? '0' : '15px' }} ">{{ $denominacion }}</span>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 150px;"><strong>FECHA DE INICIO</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $inicio ? '0' : '15px' }}">{{ date('d/m/Y', strtotime($inicio)) }}</span> <span style="display: inline-block;width: 15px;"></span><span style="display: inline-block;width: 150px;"><strong>FECHA DE INICIO</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $fin ? '0' : '15px' }}">{{ date('d/m/Y', strtotime($fin)) }}</span>
    </div>
</div>
<div class="section-title">DIRECTOR</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 150px;"><strong>Apellido y Nombres</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 545px; padding-top: {{ $director ? '0' : '15px' }}">{{ $director }}</span>
    </div>
</div>

<div class="section-title">BAJA - IDENTIFICACION DEL {{$tipoIntegrante}}</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 150px;"><strong>Apellido y Nombres</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 545px; padding-top: {{ $integrante ? '0' : '15px' }}">{{ $integrante }}</span>
    </div>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 50px;"><strong>CUIL</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $cuil ? '0' : '15px' }}">{{ $cuil }}</span> <span style="display: inline-block;width: 105px;"></span><span style="display: inline-block;width: 100px;"><strong>Categoría SPU</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 50px; padding-top: {{ $categoria_spu ? '0' : '15px' }}" >{{ $categoria_spu }}</span><span style="display: inline-block;width: 105px;"></span><span style="display: inline-block;width: 120px;"><strong>Categoría SICADI</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 50px; padding-top: {{ $categoria_sicadi ? '0' : '15px' }}">{{ $categoria_sicadi }}</span>
    </div>
</div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 150px;"><strong>Cargo docente</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 270px; padding-top: {{ $cargo_docente ? '0' : '15px' }}">{{ $cargo_docente }}</span> <span style="display: inline-block;width: 60px;"></span><span style="display: inline-block;width: 80px;"><strong>Dedicación</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 125px; padding-top: {{ $dedicacion_docente ? '0' : '15px' }}">{{ $dedicacion_docente }}</span>
    </div>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 150px;"><strong>Carrera del Inv.</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 270px; padding-top: {{ $carrera_inv ? '0' : '15px' }}">{{ $carrera_inv }}</span> <span style="display: inline-block;width: 30px;"></span><span style="display: inline-block;width: 80px;"><strong>Organismo</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 155px; padding-top: {{ $organismo ? '0' : '15px' }}">{{ $organismo }}</span>
    </div>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 60px;"><strong>Becario</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 35px">{{ $becario }}</span>
        <span style="display: inline-block;width: 45px;"><strong>Tipo</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 270px; padding-top: {{ $beca ? '0' : '15px' }}">{{ $beca }}</span> <span style="display: inline-block;width: 30px;"></span><span style="display: inline-block;width: 80px;"><strong>Institución</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 155px; padding-top: {{ $institucion ? '0' : '15px' }}">{{ $institucion }}</span>
    </div>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 150px;"><strong>Inicio beca</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $alta_beca ? '0' : '15px' }}">{{ ($alta_beca)?date('d/m/Y', strtotime($alta_beca)):'' }}</span> <span style="display: inline-block;width: 200px;"></span><span style="display: inline-block;width: 80px;"><strong>Fin beca</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 155px; padding-top: {{ $baja_beca ? '0' : '15px' }}">{{ ($baja_beca)?date('d/m/Y', strtotime($baja_beca)):'' }}</span>
    </div>
</div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 150px;font-size: 16px;"><strong>FECHA DE BAJA</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $fecha ? '0' : '15px' }}">{{ date('d/m/Y', strtotime($fecha)) }}</span>
    </div>
</div>
<div class="section-title">EN CASO DE OTORGARSE LA BAJA SOLICITADA</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100%;"><strong>IMPORTANTE: con respecto a las solicitudes de bajas se debe tener en cuenta que, a los efectos del cobro
de incentivos, el Ministerio no permite que los docentes investigadores cambien de proyecto. Cada docente
investigador es asociado a un proyecto hasta su finalización y no puede solicitar incentivos por otro
proyecto.</strong></span>
    </div>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100%;"><strong>Explicar las consecuencias de la baja en el desenvolvimiento del proyecto</strong></span>
    </div>
</div>
<div class="content">
    <span class="content-border-full" style="padding-top: {{ $consecuencias ? '0' : '15px' }}">{{ $consecuencias }}</span>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100%;"><strong>Explicar los motivos de la baja</strong></span>
    </div>
</div>
<div class="content">
    <span class="content-border-full" style="padding-top: {{ $motivos ? '0' : '15px' }}">{{ $motivos }}</span>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100%;"><strong>Considerando la baja, el proyecto cumple con los siguientes requisitos</strong></span>
    </div>
</div>
<div class="content">
    <span class="content-border-full" >La suma de dedicaciones horarias de los miembros del proyecto es igual o mayor a 30 hs. semanales.<br>Se cumple con las pautas fijadas en la Acreditación</span>
</div>

<div class="section-title">CONSENTIMIENTO DEL INTERESADO</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100%;"><strong>Dejo constancia que otorgo mi conformidad</strong></span>
    </div>
</div>
<div class="signature">
    <div class="signature-line">Lugar y Fecha</div>
    <div class="signature-line">Firma y Aclaración</div>
</div>
<div class="signature">
    <div class="signature-line">Lugar y Fecha</div>
    <div class="signature-line">Firma del Director del Proyecto</div>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100%;"><strong>La información detallada en esta solicitud tiene carácter de DECLARACION JURADA.
</strong></span>
    </div>
</div>
</body>
</html>
