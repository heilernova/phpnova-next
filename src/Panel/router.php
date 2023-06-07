<?php

use Phpnova\Next\Config;
use Phpnova\Next\Http\Attributes\Body;
use Phpnova\Next\Http\Request;
use Phpnova\Next\Panel\Dto\CreateUserDto;
use Phpnova\Next\Panel\Dto\CredentialsDto;
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

    router::post("/", function(CreateUserDto $user){

        return $user;
    });

    router::get("/:id", function(int $id = null){
        return ["informacion del usuario $id"];
    });
});