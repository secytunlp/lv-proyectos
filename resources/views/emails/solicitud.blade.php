<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación de Envío de Solicitud</title>
</head>
<body style="font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 11px; color: #666666; background-color: #FFFFFF; padding: 30px;">
<img src="{{ url('/images/secyt.gif') }}" alt="Logo" style="margin-bottom: 20px;">
<h2 style="color: #333333;">PROYECTOS DE INVESTIGACIÓN</h2>
<hr style="color: #999999; text-decoration: none;">
<p>
    <strong>{{ $asunto }}</strong><br>
    <strong>Proyecto</strong>: {{ $codigo }}<br>
    <strong>{{ $integranteMail }}</strong>: {{ $integrante }}<br>
    <strong>F. de {{ $tipo }}</strong>: {{ $fecha }}<br>
    {{ $comment }}
</p>
<hr style="color: #999999; text-decoration: none;">
</body>
</html>

