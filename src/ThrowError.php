<?php
namespace Phpnova\Next;

use Exception;
use Throwable;

class ThrowError extends Exception
{
    public function __construct(Throwable|string $error)
    {
        if ($error instanceof Throwable){
            $this->message = $error->getMessage();
            $this->code = $error->getCode();
        } else {
            $this->message = $error;
        }

        # Modificamos el archivo y la linea para que muestre el donde se ejecuta la función que crea el error
        $backtrace = debug_backtrace()[1] ?? null;
        if ($backtrace) {
            $this->file = $backtrace['file'];
            $this->line = $backtrace['line'];
        }
    }
}