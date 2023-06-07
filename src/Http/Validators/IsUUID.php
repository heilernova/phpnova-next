<?php
namespace Phpnova\Next\Http\Validators;

use Attribute;
use Phpnova\Next\ThrowError;

#[Attribute()]
class IsUUID extends Validator
{
    public function __construct()
    {
        
    }

    public function exec($val): string
    {
        $pattern = "/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/";
        if (preg_match($pattern, $val)){
            return $val;
        }

        throw new ThrowError("El valor no es UUID");
    }
}