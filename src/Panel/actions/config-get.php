<?php

use Phpnova\Next\APIConfig;

return function(APIConfig $config){
    return [
        'version' => $config->getVersion(),
        'timezone' => $config->getTimezone(),
        'debug' => $config->isDebug(),
        'private_keys' => array_keys($config->get('private_keys'))
    ];
};