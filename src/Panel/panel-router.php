<?php

use Phpnova\Next\APIConfig;
use Phpnova\Next\Config;
use Phpnova\Next\Http\Attributes\Body;
use Phpnova\Next\Http\HttpExeption;
use Phpnova\Next\Routing\Router as router;

router::get('/', function(){
    return 'Aplicacion';
});

router::post('sign-in', require __DIR__ . '/actions/sign-in-post.php');
router::get('timezones', require __DIR__ . '/actions/timezones-get.php');
router::get('config', require __DIR__ . '/actions/config-get.php');
router::put('config', require __DIR__ . '/actions/config-put.php');

// router::get('config', function(APIConfig $config){
//     return [
//         "version" => $config->getVersion(),
//         "timezone" => $config->getTimezone(),
//         "debug" => $config->isDebug()
//     ];
// });


// router::use("databases", function(){
//     router::get('/', function(Config $config){
//         return $config->getDatabases()->getAll();
//     });
//     router::post('/', function(#[Body()]object $body, Config $config){
//         $config->getDatabases()->add($body->name, $body->type, $body->host, $body->user, $body->password, $body->database, $body->port ?? null);
//         return $body;
//     });
    
//     router::get('/:name', function(Config $config, string $name ){
//         try {
//             return $config->getDatabases()->get($name);
//         } catch (\Throwable $th) {
//             throw new HttpExeption("Base de datos no encotrada", 404);
//         }
//     });
// });