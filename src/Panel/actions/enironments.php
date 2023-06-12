<?php

use Phpnova\Next\APIConfig;
use Phpnova\Next\Http\Attributes\Body;
use Phpnova\Next\Routing\Router;

return function(){
    Router::get("/", function(APIConfig $config){
        return $config->getEnvironments();
    });

    Router::post("/", function(APIConfig $config, #[Body]object $body){
        $config->getEnvironments()->set($body->name, $body->value);
    });

    Router::put("/:name", function(APIConfig $config, string $name, #[Body]object $body){
        $env = $config->getEnvironments();
        $env->set($name, $body->value);
    });
};