<?php

use Phpnova\Next\APIConfig;

return function(APIConfig $config){

    $environments = [];

    foreach($config->getEnvironments()->getAll() as $key => $value){
        $environments[] = ['name' => $key, 'value' => $value];
    }

    return [
        'version' => $config->getVersion(),
        'timezone' => $config->getTimezone(),
        'debug' => $config->isDebug(),
        'private_keys' => array_keys($config->get('private_keys')),
        'environments' => $environments
    ];
};