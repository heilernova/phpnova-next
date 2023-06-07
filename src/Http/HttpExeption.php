<?php
namespace Phpnova\Next\Http;

use ErrorException;
use Phpnova\Next\ThrowError;

class HttpExeption extends ErrorException
{
    public function __construct(string $message, int $code = 400)
    {
        if ($code > 499 && $code < 400){
            throw new ThrowError( "Solo se permiten códigos de error 400" );
        }

        $this->message = $message;
        $this->code = $code;
    }
}