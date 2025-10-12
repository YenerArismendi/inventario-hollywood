<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Configura los ajustes de CORS para definir qué orígenes y métodos pueden
    | acceder a tu aplicación. Esto es especialmente importante si usas Sanctum
    | o inicias sesión desde un frontend (React, Vue, etc.) separado.
    |
    | Más info: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login',
        'logout'
    ],

    /*
    |--------------------------------------------------------------------------
    | Métodos permitidos
    |--------------------------------------------------------------------------
    |
    | Puedes permitir todos los métodos HTTP con ['*'] o restringirlos según
    | necesidad (por ejemplo: ['GET', 'POST', 'PUT', 'DELETE']).
    |
    */

    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Orígenes permitidos
    |--------------------------------------------------------------------------
    |
    | Define los dominios desde donde se pueden hacer solicitudes.
    | En este caso, se toma desde la variable FRONTEND_URLS del .env.
    | Ejemplo:
    | FRONTEND_URLS=http://localhost:3000,http://127.0.0.1:3000
    |
    */

    'allowed_origins' => explode(',', env('FRONTEND_URLS', 'http://localhost:3000')),

    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Encabezados permitidos
    |--------------------------------------------------------------------------
    |
    | Define los encabezados (headers) que la solicitud puede incluir.
    | '*' significa todos los encabezados son aceptados.
    |
    */

    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Encabezados expuestos
    |--------------------------------------------------------------------------
    |
    | Define qué encabezados pueden ser leídos por el cliente.
    |
    */

    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Tiempo máximo de caché
    |--------------------------------------------------------------------------
    |
    | Especifica cuánto tiempo el navegador puede almacenar los resultados de
    | una solicitud CORS previa (en segundos). 0 significa sin caché.
    |
    */

    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Soporte para credenciales
    |--------------------------------------------------------------------------
    |
    | Debe estar en true si envías cookies o cabeceras de autorización.
    | Necesario cuando usas Laravel Sanctum con un frontend separado.
    |
    */

    'supports_credentials' => true,

];
