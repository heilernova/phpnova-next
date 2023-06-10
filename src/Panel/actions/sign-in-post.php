<?php

use Phpnova\Next\APIConfig;
use Phpnova\Next\Http\Attributes\Body;
use Phpnova\Next\Http\HttpExeption;
use Phpnova\Next\Utils\jwt;

return function(#[Body]object $credentials, APIConfig $config){
    $credentials->username;
    $credentials->password;
    $user = $config->getUser();

    if ($credentials->username == $user['username'] && password_verify($credentials->password, $user['password'])){
        return jwt::encode(['exp' => time() * 60 * 60 * 4]);
    } else {
        throw new HttpExeption("Credenciales incorrectas", 400);
    }
};