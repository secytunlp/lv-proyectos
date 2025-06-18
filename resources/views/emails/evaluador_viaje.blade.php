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
<h2 style="color: #333333;">Evaluación de Subsidios para Subsidios para Viajes y/o Estadías ({{$mes_desde}} {{ $year }} - {{$mes_hasta}} {{ intval($year)+1 }})</h2>
<hr style="color: #999999; text-decoration: none;">
<p>La Plata, {{$fecha}}</p>
<p>Estimado/a Evaluadora/or {{$tipo}}: {{$evaluador}}</p>

<p>
    Tenemos el agrado de dirigirnos a usted a fin de invitarlo a participar en el proceso de evaluación de la convocatoria de Solicitud de Subsidios para Subsidios para Viajes y/o Estadías ({{$mes_desde}} {{ $year }} - {{$mes_hasta}} {{ intval($year)+1 }}).
    Su participaci&oacute;n como evaluador es de vital importancia para el Programa de Subsidios de la Universidad.
</p>
<p><strong>EVALUACI&Oacute;N ASIGNADA</strong></p>
<p>Le hemos asignado la siguiente solicitud para su evaluaci&oacute;n:</p>
<p>Postulante: {{$postulante}}</p>
<p><strong>SOBRE LA EVALUACIÓN</strong></p>
<p><strong>Le pedimos, en la medida de lo posible, que nos envíe las evaluaciones por sistema lo antes posible,</strong> con el fin de realizar los procesos administrativos necesarios para otorgar los subsidios en tiempo y forma</p>
<p>
    Asimismo requerimos nos comunique su aceptación o rechazo dentro de las 48 hs. de recibida esta comunicación. Debe hacerlo por medio del sistema,
    ingresando <a href="{urlWeb}">aquí</a> donde deberá seleccionar la solicitud a evaluar, para luego aceptarla o rechazarla.
</p>
<p>
    Le aclaramos que cada solicitud tendrá asignado un evaluador interno (el mismo deberá pertenecer a la Unidad Académica del solicitante)
    y un evaluador externo (que podría no relacionarse estrictamente con la temática del solicitante).
</p>
<br>


<!-- <p>
Para más información puede acceder al instructivo del evaluador que se encuentra disponible <a href="{{$urlInstructivo}}">aquí</a>.
</p> -->


<p><strong>CONSULTAS</strong></p>
<p>Para cualquier consulta puede comunicarse con los coordinadores de Subsidios para Viajes: <br><br>
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

