<?php

use Phpnova\Next\Config;
use Phpnova\Next\Http\Attributes\Body;
use Phpnova\Next\Http\HttpExeption;
use Phpnova\Next\Routing\Router as router;

router::get('/', function(){
    return 'Aplicacion';
});

router::post('sign-in', function(CredentialsDto $credentials){
    return $credentials;
});

router::get('config', function(Config $config){
    return [
        "version" => $config->getVersion(),
        "timezone" => $config->getTimezone(),
        "debug" => $config->isDebug()
    ];
});

router::use("users", function(){
    router::get("/", function(Config $config){
        return $config->getUsers();
    });

    router::post("/", function(#[Body]object $user){
        return $user;
    });

    router::get("/:id", function(int $id = null){
        return ["informacion del usuario $id"];
    });
});

router::use("databases", function(){
    router::get('/', function(Config $config){
        return $config->getDatabases()->getAll();
    });
    router::post('/', function(#[Body()]object $body, Config $config){
        $config->getDatabases()->add($body->name, $body->type, $body->host, $body->user, $body->password, $body->database, $body->port ?? null);
        return $body;
    });
    
    router::get('/:name', function(Config $config, string $name ){
        try {
            return $config->getDatabases()->get($name);
        } catch (\Throwable $th) {
            throw new HttpExeption("Base de datos no encotrada", 404);
        }
    });
});