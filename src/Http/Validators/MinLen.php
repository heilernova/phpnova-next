<?php
namespace Phpnova\Next\Http\Validators;

use Attribute;
use Phpnova\Next\ThrowError;

#[Attribute()]
class MinLen extends Validator
{
    public function __construct(public readonly int $len)
    {
        
    }

    public function exec($val)
    {
        if (is_string($val)){
            $len = strlen($val);
            if ($len >= $this->len){
                return $val;
            } else {
                throw new ThrowError("La longitud del contenido es menor que la permitida: [Longitid minima: $this->len] [Lognitud del contenido: $len]");
            }
        } else {
            throw new ThrowError('El valor debe ser un string');
        }
    }
}