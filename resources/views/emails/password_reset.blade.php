<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Recuperación de Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #dc3545;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 0 0 5px 5px;
            border: 1px solid #ddd;
            border-top: none;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white !important;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            margin-top: 20px;
            font-size: 0.9em;
            color: #666;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Gestión de Obras - Recuperación de Contraseña</h2>
    </div>
    <div class="content">
        <h3>¡Hola {{ $user->name }}!</h3>
        <p>Recibimos una solicitud para restablecer tu contraseña.</p>
        <p>Haz clic en el siguiente botón para restablecer tu contraseña:</p>

        <center>
            <a href="{{ $resetUrl }}" class="button">Restablecer Contraseña</a>
        </center>

        <p>Este enlace expirará en {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutos.</p>

        <p>Si no solicitaste restablecer tu contraseña, por favor ignora este mensaje.</p>

        <p>Si tienes problemas con el botón, copia y pega la siguiente URL en tu navegador:</p>
        <p><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} Gestión de Obras. Todos los derechos reservados.</p>
    </div>
</body>
</html>
