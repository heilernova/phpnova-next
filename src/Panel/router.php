<?php

use Phpnova\Next\Config;
use Phpnova\Next\Http\Attributes\Body;
use Phpnova\Next\Http\Request;
use Phpnova\Next\Routing\Router as router;

router::get('/', function(){
    return 'Aplicacion';
});

router::post('sign-in', function(#[Body]object $credentials){
    return "Authenticarse";
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

    router::post("/", function(Config $config, Request $req){
        return $req;
        $config->addUser("Hielern", "heilernova@gmail.com", "admin");
        $config->save();
        return true;
    });
});