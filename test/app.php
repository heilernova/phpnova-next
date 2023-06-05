<?php

use App\Resources\Authentication\AuthenticationController;
use Phpnova\Next\Factory\AppFactory;
use Phpnova\Next\Routing\Router;

require __DIR__ . '/../vendor/autoload.php';

$app =  AppFactory::create(dir: __DIR__, enablePanel: true);
$app->run();