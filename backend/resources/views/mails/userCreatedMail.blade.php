@extends('layouts.mail')

<div class="main-div">
    <div style="padding: 2em">
        <div style="background-color: lightseagreen; color: white; text-align: center; padding: 2em; font-weight: 600;">
            Los Boldos Rov
        </div>
        <h3>Hola {{ $name }}. Se ha creado un nuevo usuario para usted en la plataforma Los Boldos Rovs. Para acceder a la plataforma presione el siguiente enlace:</h2>
            <div class="boton-container" style="margin-top: 1cm; text-align: center;">
                <a href="{{ $url }}" style="">Acceder al Sitio</a>
            </div>
            <h3>Credenciales</h1>
                <table>
                    <thead>
                        <tr>
                            <th>
                                Nombre de Usuario
                            </th>
                            <th>
                                Contraseña
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                {{ $username }}
                            </td>
                            <td>
                                {{ $password }}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <h3></h3>
                <p>Nota: Se recomienda cambiar la
                    contraseña desde el perfil de usuario.</p>
    </div>
</div>

<style>
    .main-div {
        background-color: #ffffff;
        padding: 3rem;
        max-width: 60rem;
        margin-right: auto;
        margin-left: auto;
        border-radius: 0.375rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .main-div>h1 {
        font-size: 2rem;
        font-weight: 600;
        margin-bottom: 1rem;
        font-weight: 600;
        text-align: center;
    }

    .main-div>p {
        color: #040404;
        text-align: center;
        margin-top: 0.75cm;
    }
</style>
{{-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Nuevo Usuario Creado</title>
</head>
<body>
    <h2>Hola {{ $name }}. Se ha creado un nuevo usuario para usted en la plataforma Los Boldos Rovs</h2>

    <h2>Credenciales</h1>
    <table>
        <thead>
            <tr>
                <th>
                    Nombre de Usuario
                </th>
                <th>
                    Contraseña
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $username }}
                </td>
                <td>
                    {{ $password }}
                </td>
            </tr>
        </tbody>
    </table>
    <h3>Nota: Se recomienda cambiar la contraseña desde el perfil de usuario</h3>
</body>
</html> --}}
