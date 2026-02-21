<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cuenta Aprobada</title>
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
            background-color: #28a745;
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
            background-color: #28a745;
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
        <h2>Gestión de Obras - Cuenta Aprobada</h2>
    </div>
    <div class="content">
        <h3>¡Hola {{ $user->name }}!</h3>
        <p>¡Buenas noticias! Tu cuenta en el sistema de Gestión de Obras ha sido aprobada por un administrador.</p>

        <p>Ahora puedes acceder al sistema con tus credenciales:</p>

        <center>
            <a href="{{ $loginUrl }}" class="button">Iniciar Sesión</a>
        </center>

        <p>Si tienes problemas con el botón, copia y pega la siguiente URL en tu navegador:</p>
        <p><a href="{{ $loginUrl }}">{{ $loginUrl }}</a></p>

        <p>Si no solicitaste esta aprobación o tienes alguna pregunta, por favor contacta con el administrador del sistema.</p>
    </div>
    <div class="footer">
        <p>© {{ date('Y') }} Gestión de Obras. Todos los derechos reservados.</p>
    </div>
</body>
</html>
