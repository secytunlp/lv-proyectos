<!DOCTYPE html>
<html>
<head>
    <title>Solicitud</title>
    <style>

        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            margin-top: 230px; /* Asegúrate de dejar espacio para el header */
            padding: 0;
        }


        .header {
            position: fixed;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;

        }

        .header-top {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            height: 50px; /* <<< ESTO es lo importante: le das altura */
        }

        .logoSecyt, .logoSicadi {
            display: table-cell;
            vertical-align: middle;
            width: 50%; /* Cada logo ocupa la mitad */
        }

        .logoSecyt img {
            float: left; /* Logo de la izquierda */
            height: 60px;
        }

        .logoSicadi img {
            float: right; /* Logo de la derecha */
            height: 60px;
        }

        /* Parte de abajo: título a la izquierda, foto a la derecha */
        .header-bottom {
            display: table;
            width: 100%;
            table-layout: fixed;

        }

        .text-title, .foto {
            display: table-cell;
            vertical-align: middle;
        }

        .text-title {
            width: 70%;
            text-align: left;
            font-size: 18px;
            /*font-weight: bold;*/
            padding-right: 10px;
        }

        .foto {
            width: 30%;
            text-align: right;
        }

        .foto img {
            height: 150px;
        }


        .text-content {
            display: table;
            width: 100%;
            text-align: center;
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

        footer {
            position: fixed;
            bottom: -60px;
            left: 0px;
            right: 0px;
            height: 50px;
            font-size: 10px;
            text-align: right;
            color: #999;
            border-top: 1px solid #ccc;
            padding-top: 5px;
        }

    </style>
</head>
<body>

<?php //dd($objetivo); ?>
<?php if ($estado === 'Creada'): ?>
    <div class="watermark">VISTA_PRELIMINAR</div>
<?php endif; ?>
<div class="header">
    <div class="header-top">
        <div class="logoSecyt">
            <img src="{{ public_path('/images/logo_secyt.PNG') }}" alt="SECYT Logo" class="logo">
        </div>
        <div class="logoSicadi">
            <img src="{{ public_path('/images/logo_sicadi.PNG') }}" alt="SICADI Logo" class="logo">
        </div>
    </div>
    <div class="header-bottom">
        <div class="text-title">
            Sistema de Categorización de Docentes Investigadores<br>
            de la Universidad Nacional de La Plata<br>
            <strong>{{$convocatoria}}</strong> <!-- Agregado el <strong> -->
        </div>
        @if (!empty($foto) && file_exists(public_path('/images/sicadi/' . $foto)))
            <div class="foto">
                <img src="{{ public_path('/images/sicadi/' . $foto) }}" alt="Foto" class="logo">
            </div>
        @endif
    </div>
</div>

<footer>

    {{date('d/m/Y H:i:s', strtotime($fecha))}}
</footer>


<div class="content">
    <div class="content" style="margin-top: 10px;">
        <strong>1. DATOS PERSONALES</strong>
    </div>

</div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Apellido y Nombres</span> <span style="display: inline-block; border: 1px solid #ccc;width: 350px; padding-top: {{ $solicitante ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $solicitante }}</span><span style="display: inline-block;width: 55px;"></span>
        <span style="display: inline-block;width: 40px;">CUIL</span> <span style="display: inline-block; border: 1px solid #ccc;width: 110px; padding-top: {{ $cuil ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $cuil }}</span>
    </div>

</div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Género</span> <span style="display: inline-block; border: 1px solid #ccc;width: 150px; padding-top: {{ $genero ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $genero }}</span><span style="display: inline-block;width: 200px;"></span>
        <span style="display: inline-block;width: 96px;">F. Nacimiento</span> <span style="display: inline-block; border: 1px solid #ccc;width: 108px; padding-top: {{ $nacimiento ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($nacimiento)?date('d/m/Y', strtotime($nacimiento)):'' }}</span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Celular</span> <span style="display: inline-block; border: 1px solid #ccc;width: 200px; padding-top: {{ $telefono ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $telefono }}</span><span style="display: inline-block;width: 150px;"></span>
        <span style="display: inline-block;width: 96px;">Nacionalidad</span> <span style="display: inline-block; border: 1px solid #ccc;width: 108px; padding-top: {{ $nacionalidad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $nacionalidad}}</span>
    </div>
</div>
<div class="content">
    <!--<div>Domicilio de notificación (Dentro del Radio Urbano de La Plata, Art. 20 Ord. 101)</div>-->
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Calle</span> <span style="display: inline-block; border: 1px solid #ccc;width: 185px; padding-top: {{ $calle ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $calle }}</span>
        <span style="display: inline-block;width: 35px;">Nro.</span> <span style="display: inline-block; border: 1px solid #ccc;width: 45px; padding-top: {{ $nro ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $nro }}</span>
        <span style="display: inline-block;width: 35px;">Piso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 45px; padding-top: {{ $piso ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $piso }}</span>
        <span style="display: inline-block;width: 40px;">Dpto.</span> <span style="display: inline-block; border: 1px solid #ccc;width: 45px; padding-top: {{ $depto ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $depto }}</span>
        <span style="display: inline-block;width: 35px;">C.P.</span> <span style="display: inline-block; border: 1px solid #ccc;width: 60px; padding-top: {{ $cp ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $cp }}</span>
    </div>

</div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">E-mail institucional</span> <span style="display: inline-block; border: 1px solid #ccc;width: 564px; padding-top: {{ $email ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $email }}</span>

    </div>
    @if(intval($year)>2012)
    <div style="font-size: 10px;">Acepto recibir toda notificación relativa a la presente solicitud en la dirección de correo electrónico declarada
        precedentemente {{ $notificacion }}</div>
    @endif
</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">E-mail alternativo</span> <span style="display: inline-block; border: 1px solid #ccc;width: 564px; padding-top: {{ $alternativo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $alternativo }}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Perfil SEDICI</span> <span style="display: inline-block; border: 1px solid #ccc;width: 564px; padding-top: {{ $sedici ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $sedici }}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Número de ORCID</span> <span style="display: inline-block; border: 1px solid #ccc;width: 564px; padding-top: {{ $orcid ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $orcid }}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Perfil de Google Académico</span> <span style="display: inline-block; border: 1px solid #ccc;width: 564px; padding-top: {{ $scholar ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $scholar }}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Número de Scopus</span> <span style="display: inline-block; border: 1px solid #ccc;width: 564px; padding-top: {{ $scopus ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $scopus }}</span>

    </div>

</div>
<div style="font-size: 10px;font-style: italic">La Información vertida en los campos de dirección, teléfono, fecha de nacimiento, foto
    y correos electrónicos se preservará y no será divulgada a terceras partes.</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <strong>2. DATOS ACADÉMICOS</strong>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Título de grado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 300px; padding-top: {{ $titulo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $titulo }}</span>
        <span style="display: inline-block;width: 85px;">E. Otorgante</span> <span style="display: inline-block; border: 1px solid #ccc;width: 170px; padding-top: {{ $titulo_entidad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $titulo_entidad}}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Título de posgrado</span> <span style="display: inline-block; border: 1px solid #ccc;width: 300px; padding-top: {{ $tituloposgrado ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $tituloposgrado }}</span>
        <span style="display: inline-block;width: 85px;">E. Otorgante</span> <span style="display: inline-block; border: 1px solid #ccc;width: 170px; padding-top: {{ $posgrado_entidad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $posgrado_entidad}}</span>
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
        <span style="display: inline-block;width: 220px;">UA donde tiene el Cargo docente</span> <span style="display: inline-block; border: 1px solid #ccc;width: 475px; padding-top: {{ $facultad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $facultad }}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        Lugar de Trabajo donde realiza tareas de investigación

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block; border: 1px solid #ccc;width: 695px; padding-top: {{ $unidad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $unidad }}</span>

    </div>

</div>
<!--<div class="page-break"></div>--> <!-- Esto genera el salto de página -->
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <strong>3. INVESTIGACIÓN</strong>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;margin-left:20px;">
        <strong>3.A Becas</strong>
    </div>

</div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Tipo de beca</span> <span style="display: inline-block; border: 1px solid #ccc;width: 300px; padding-top: {{ $beca_tipo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $beca_tipo }}</span>
        <span style="display: inline-block;width: 85px;">E. Otorgante</span> <span style="display: inline-block; border: 1px solid #ccc;width: 170px; padding-top: {{ $beca_entidad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $beca_entidad}}</span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Fecha de Inicio</span> <span style="display: inline-block; border: 1px solid #ccc;width: 170px; padding-top: {{ $beca_inicio ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($beca_inicio)?date('d/m/Y', strtotime($beca_inicio)):'' }}</span><span style="display: inline-block;width: 130px;"></span>
        <span style="display: inline-block;width: 85px;">Fecha de Fin</span> <span style="display: inline-block; border: 1px solid #ccc;width: 170px; padding-top: {{ $beca_fin ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($beca_fin)?date('d/m/Y', strtotime($beca_fin)):'' }}</span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        Lugar de Trabajo donde realiza tareas de investigación

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block; border: 1px solid #ccc;width: 695px; padding-top: {{ $unidad_beca ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $unidad_beca }}</span>

    </div>

</div>
<div style="font-size: 8px;font-style: italic"><strong>IMPORTANTE: Las/los becarias/os que NO son UNLP deberán adjuntar a esta presentación una certificación de tareas como
    becaria/o emitida por la entidad otorgante (CONICET /AGENCIA/CICPBA/Otros) con vigencia en la fecha de la presente
    convocatoria. Alternativamente se aceptará un recibo de estipendio del corriente mes. No es válida la resolución de otorgamiento
        de la beca.</strong></div>
<div class="content">
    <div class="content" style="margin-top: 10px;margin-left:20px;">
        <strong>3.B Investigador/a o Profesional de Apoyo (CPA)</strong>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 60px;">Cargo</span> <span style="display: inline-block; border: 1px solid #ccc;width: 190px; padding-top: {{ $carrera_cargo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $carrera_cargo }}</span>
        <span style="display: inline-block;width: 75px;">Empleador</span> <span style="display: inline-block; border: 1px solid #ccc;width: 170px; padding-top: {{ $carrera_empleador ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $carrera_empleador}}</span>
        <span style="display: inline-block;width: 60px;">Ingreso</span> <span style="display: inline-block; border: 1px solid #ccc;width: 120px; padding-top: {{ $carrera_ingreso ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($carrera_ingreso)?date('d/m/Y', strtotime($carrera_ingreso)):'' }}</span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        Lugar de Trabajo donde realiza tareas de investigación

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block; border: 1px solid #ccc;width: 695px; padding-top: {{ $unidad_carrera ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $unidad_carrera }}</span>

    </div>

</div>
<div style="font-size: 8px;font-style: italic"><strong>IMPORTANTE: Se deberá adjuntar a esta presentación una certificación de trabajo como Investigadora/or o CPA,
        emitida por el empleador con vigencia en la fecha de la presente convocatoria. Alternativamente se aceptará un
        recibo de sueldo del corriente mes. No es válida la resolución de ingreso.</strong></div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <strong>4. PROYECTO DE INVESTIGACIÓN</strong>
    </div>

</div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 85px;">E. Otorgante</span> <span style="display: inline-block; border: 1px solid #ccc;width: 150px; padding-top: {{ $proyecto_entidad ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyecto_entidad }}</span>
        <span style="display: inline-block;width: 50px;">Código</span> <span style="display: inline-block; border: 1px solid #ccc;width: 100px; padding-top: {{ $proyecto_codigo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyecto_codigo}}</span>
        <span style="display: inline-block;width: 50px;">Director</span> <span style="display: inline-block; border: 1px solid #ccc;width: 240px; padding-top: {{ $proyecto_director ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyecto_director}}</span>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Título</span> <span style="display: inline-block; border: 1px solid #ccc;width: 564px; padding-top: {{ $orcid ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $proyecto_titulo }}</span>

    </div>

</div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 130px;">Fecha de Inicio</span> <span style="display: inline-block; border: 1px solid #ccc;width: 170px; padding-top: {{ $proyecto_inicio ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($proyecto_inicio)?date('d/m/Y', strtotime($proyecto_inicio)):'' }}</span><span style="display: inline-block;width: 130px;"></span>
        <span style="display: inline-block;width: 85px;">Fecha de Fin</span> <span style="display: inline-block; border: 1px solid #ccc;width: 170px; padding-top: {{ $proyecto_fin ? '0' : '15px' }}; background-color: #e1e1e1;">{{ ($proyecto_fin)?date('d/m/Y', strtotime($proyecto_fin)):'' }}</span>
    </div>

</div>

<div class="content">
    <div class="content" style="margin-top: 10px;">
        <strong>5. CATEGORIZACIÓN</strong>
    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 190px;">UA por la que se presenta</span> <span style="display: inline-block; border: 1px solid #ccc;width: 505px; padding-top: {{ $presentacion_ua ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $presentacion_ua }}</span>

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 190px;">Categoría Actual SPU</span> <span style="display: inline-block; border: 1px solid #ccc;width: 40px; padding-top: {{ $categoria_spu ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $categoria_spu }}</span>
        <span style="display: inline-block;width: 130px;">Categoría Solicitada</span> <span style="display: inline-block; border: 1px solid #ccc;width: 40px; padding-top: {{ $categoria_solicitada ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $categoria_solicitada}}</span>
        @if($tipo==='Equivalencia')
            <span style="display: inline-block;width: 75px;">Mecanismo</span> <span style="display: inline-block; border: 1px solid #ccc;width: 200px; padding-top: {{ $mecanismo ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $mecanismo}}</span>
        @endif

    </div>

</div>
<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 190px;">Área</span> <span style="display: inline-block; border: 1px solid #ccc;width: 200px; padding-top: {{ $area ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $area}}</span><span style="display: inline-block;width: 10px;"></span>

        <span style="display: inline-block;width: 60px;">Subárea</span> <span style="display: inline-block; border: 1px solid #ccc;width: 225px; padding-top: {{ $subarea ? '0' : '15px' }}; background-color: #e1e1e1;">{{ $subarea}}</span>
    </div>

</div>


<div class="content">
    <div class="content" style="margin-top: 10px;">
        <span style="display: inline-block;width: 100%;"><strong>La información provista por la/el DI en la planilla de solicitud por equivalencia, será considerada
                una declaración jurada.</strong></span>
    </div>
</div>

<div class="signature">
    <div class="signature-line">{{$solicitante}} </div>

</div>
<div class="signature">

    <div class="signature-line">Certificación
        de cargo docente, lugar de trabajo y proyecto
        Secretaría de Ciencia y Técnica
        (o equivalente) de la Unidad Académica</div>
</div>


</body>
</html>
