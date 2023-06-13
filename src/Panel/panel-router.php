<?php

use Phpnova\Next\APIConfig;
use Phpnova\Next\Http\HttpExeption;
use Phpnova\Next\Http\Request;
use Phpnova\Next\Routing\Router as router;
use Phpnova\Next\Utils\jwt;

router::get('/', function(){
    return 'Aplicacion';
});

# Verificamos la autenticación
// router::use(function(Request $req){
//     $token = $req->headers["Authorization"] ?? $req->headers['authorization'] ?? null;
//     if ($token && jwt::verify(substr($token, 7))){
//         return null;
//     }
//     throw new HttpExeption("Se require autorización", 401);
// });

router::post('sign-in', require __DIR__ . '/actions/sign-in-post.php');
router::get('timezones', require __DIR__ . '/actions/timezones-get.php');
router::get('config', require __DIR__ . '/actions/config-get.php');
router::put('config', require __DIR__ . '/actions/config-put.php');
router::use("environments", require __DIR__ . '/actions/enironments.php');