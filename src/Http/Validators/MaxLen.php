<?php
namespace Phpnova\Next\Http\Validators;

use Attribute;
use Phpnova\Next\ThrowError;

#[Attribute()]
class MaxLen extends Validator
{
    public function __construct(private readonly int $len)
    {
        
    }

    public function exec($val): bool
    {
        if (is_string($val)){
            $len = strlen($val);
            if ($len <= $this->len){
                return $val;
            } else {
                throw new ThrowError("La longitud del contenido superada la permitida: [Longitid permitida: $this->len] [Lognitud del contenido: $len]");
            }
        } else {
            throw new ThrowError('El valor debe ser un string');
        }
    }
}