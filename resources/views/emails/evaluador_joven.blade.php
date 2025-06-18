<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Envío de Evaluación</title>
</head>
<body style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; color: #666666; background-color: #FFFFFF; padding: 30px;">
<img src="{{ url('/images/secyt.gif') }}" alt="Logo" style="margin-bottom: 20px;">
<h2 style="color: #333333;">Evaluación de Subsidios para Jóvenes Investigadores {{$year}}</h2>
<hr style="color: #999999; text-decoration: none;">
<p>La Plata, {{$fecha}}</p>
<p>Estimado/a Evaluadora/or: {{$evaluador}}</p>
<p>Tenemos el agrado de dirigirnos a Ud. con la finalidad de invitarlo a participar en el proceso de evaluaci&oacute;n de la convocatoria de
    Solicitud de Subsidios para Jóvenes Investigadores {{$year}}. Su participaci&oacute;n como evaluador es de vital importancia para el Programa de Subsidios de la Universidad.
</p>
<p><strong>EVALUACI&Oacute;N ASIGNADA</strong></p>
<p>Le hemos asignado la siguiente solicitud para su evaluaci&oacute;n:</p>
<p>Postulante: {{$postulante}}</p>
<p><strong>SOBRE LA EVALUACI&Oacute;N</strong></p>
<p>Solicitamos tenga a bien enviar el resultado de su evaluaci&oacute;n dentro de los pr&oacute;ximos 10 d&iacute;as</p>
<p>Asimismo, requerimos nos comunique su aceptaci&oacute;n o rechazo dentro de las 48 hs. de recibida esta comunicaci&oacute;n. Debe hacerlo por medio del sistema, ingresando <a href="{{$urlWeb}}">aquí</a> donde deberá seleccionar la solicitud a evaluar, para luego aceptarla o rechazarla.</p>


<!-- <p>
Para más información puede acceder al instructivo del evaluador que se encuentra disponible <a href="{{$urlInstructivo}}">aquí</a>.
</p> -->
<p>
    <strong>--	Importante: los siguientes &iacute;tems de la planilla de evaluaci&oacute;n son evaluados autom&aacute;ticamente por el sistema: </strong><br>
    A4 - Factor de Eficiencia<br>
    B1 - Cargo Docente<br>
    D8 - Lugar de Trabajo<br>
    E1 - Si no obtuvo subsidio en el per&iacute;odo anterior

</p>

<p><strong>CONSULTAS</strong></p>
<p>Para cualquier consulta puede comunicarse con los coordinadores de Subsidios para J&oacute;venes Investigadores: <br><br>
    {!!  $coordinadores !!}<br>
    O con esta Secretar&iacute;a por correo electr&oacute;nico a viajes.secyt@presi.unlp.edu.ar o al Tel&eacute;fono: 644-7006 de 8:00 a 13:00 hs.<br>
</p>
<p>
    Agradeciendo su valiosa colaboraci&oacute;n, saludamos a usted muy atentamente.<br><br>
    Secretar&iacute;a de Ciencia y T&eacute;cnica <br>
    Universidad Nacional de La Plata<br><br>
</p>
<hr style= 'color: #999999; text-decoration: none;'>
</body>
</html>

