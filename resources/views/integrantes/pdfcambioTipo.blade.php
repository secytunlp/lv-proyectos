<!DOCTYPE html>
<html>
<head>
    <title>Cambio de tipo de integrante</title>
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
<?php if ($estado === 'Cambio Tipo Creado'): ?>
    <div class="watermark">VISTA_PRELIMINAR</div>
<?php endif; ?>
<div class="header">
    <img src="{{ url('/images/secyt.gif') }}"><br>
    <div class="unit-title">
        <span>CAMBIO DE TIPO - E</span>
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

<div class="section-title">CAMBIO DE TIPO - IDENTIFICACION DEL {{$tipoIntegrante}}</div>
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
@if($estudiante=='SI')
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 80px;"><strong>Estudiante</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 35px">{{ $estudiante }}</span>
        <span style="display: inline-block;width: 55px;"><strong>Carrera</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 255px; padding-top: {{ $carrera ? '0' : '15px' }}">{{ $carrera }}</span> <span style="display: inline-block;width: 30px;"></span><span style="display: inline-block;width: 175px;"><strong>Materias Total/Adeudadas</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 45px; padding-top: {{ $total ? '0' : '15px' }}">{{ $total }}</span>
    </div>
</div>
@else
<div class="content" style="margin-top: 10px;">
    <span style="display: inline-block;width: 150px;"><strong>Título de Grado</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 545px; padding-top: {{ $titulo_grado ? '0' : '15px' }} ">{{ $titulo_grado }}</span>
</div>
<div class="content" style="margin-top: 10px;">
    <span style="display: inline-block;width: 150px;"><strong>Título de Posgrado</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 545px; padding-top: {{ $titulo_posgrado ? '0' : '15px' }} ">{{ $titulo_posgrado }}</span>
</div>
@endif
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
<div class="content" style="margin-top: 10px;">
    <span style="display: inline-block;width: 150px;"><strong>Lugar de trabajo</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 545px; padding-top: {{ $unidad ? '0' : '15px' }} ">{{ $unidad }}</span>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 160px;font-size: 16px;"><strong>FECHA DE CAMBIO</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 90px; padding-top: {{ $fecha ? '0' : '15px' }}">{{ date('d/m/Y', strtotime($fecha)) }}</span> <span style="display: inline-block;width: 15px;"></span><span style="display: inline-block;width: 80px;"><strong>Universidad</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 340px; padding-top: {{ $universidad ? '0' : '15px' }}">{{ $universidad }}</span>
    </div>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 200px;"><strong>Horas dedicadas actualmente</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 60px; padding-top: {{ $horas ? '0' : '15px' }}">{{$horas_anteriores}}</span> <span style="display: inline-block;width: 170px;"></span><span style="display: inline-block;width: 15px;"></span><span style="display: inline-block;width: 140px;"><strong>Horas solicitadas</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $tipo_investigador ? '0' : '15px' }}">{{ $horas }}</span>
    </div>
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100px;"><strong>Tipo actual</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 230px; padding-top: {{ $tipo_investigador_anterior ? '0' : '15px' }}">{{ $tipo_investigador_anterior }}</span> <span style="display: inline-block;width: 30px;"></span><span style="display: inline-block;width: 15px;"></span><span style="display: inline-block;width: 110px;"><strong>Tipo solicitado</strong></span> <span style="display: inline-block; border: 1px solid #ccc;width: 200px; padding-top: {{ $tipo_investigador ? '0' : '15px' }}">{{ $tipo_investigador }}</span>
    </div>
</div>
<div class="section-title">OTRO PROYECTO EN EL QUE PARTICIPA</div>
<div class="content">
    <table>
        <tr>
            <th>Código</th><th>Título</th><th>Director</th><th>Tipo</th><th>Período</th><th>Hs. x Sem.</th>
        </tr>
        @foreach($otroProyecto as $proyecto)
            <tr>
                <td>{{$proyecto['codigo']}}</td><td>{{$proyecto['titulo']}}</td><td>{{$proyecto['director']}}</td><td>{{$proyecto['tipo']}}</td><td>{{$proyecto['periodo']}}</td><td>{{$proyecto['horas']}}</td>
            </tr>
        @endforeach
    </table>
</div>
@if(!empty($reduccion))
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100%;"><strong>En el caso de ser una reducción horaria, especificar las consecuencias que la misma tendrá en el desarrollo
del proyecto</strong></span>
    </div>
</div>
<div class="content">
    <span class="content-border-full" style="padding-top: {{ $reduccion ? '0' : '15px' }}">{{ $reduccion }}</span>
</div>
<div class="content">
    <span class="content-border-full" ><strong>Considerando la reducción, el proyecto cumple con las pautas fijadas en la Acreditación: La suma de
dedicaciones horarias de los miembros del proyecto es igual o mayor a hs. semanales</strong></span>
</div>
@endif
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
