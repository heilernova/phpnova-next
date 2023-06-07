<?php

use App\Resources\Authentication\AuthenticationController;
use Phpnova\Next\Factory\AppFactory;
use Phpnova\Next\Http\Response;
use Phpnova\Next\Routing\Router;

require __DIR__ . '/../vendor/autoload.php';

$app =  AppFactory::create(dir: __DIR__, enablePanel: true);
$app->handleResponse(function(response $respoonse){
    $data = [
        'version' => '1.0.0',
        'response' => $respoonse->getBody()
    ];
    return new Response($data, $respoonse->getStatus());
});
$app->run();