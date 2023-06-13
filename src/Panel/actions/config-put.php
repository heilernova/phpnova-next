<?php

use Phpnova\Next\APIConfig;
use Phpnova\Next\Http\Attributes\Body;

return function(APIConfig $config, #[Body]object $body){
    $data = (array)$body;


    if (array_key_exists('private_keys', $data)){
        $keys = $config->get('private_keys');
        foreach ($data['private_keys'] as $key){
            $keys[$key] = bin2hex(openssl_random_pseudo_bytes(25));
        }
        $data['private_keys'] = $keys;
    }

    $config->update($data);
    return true;
};