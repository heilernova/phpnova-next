<?php
// namespace Phpnova\Next\Http\Validators;
namespace Phpnova\Next\Http\Validators;

use Attribute;
use Error;
use Phpnova\Next\ThrowError;

#[Attribute()]
class IsEmail extends Validator
{
    /**
     * @param 'lowercase'| 'uppercase' | null $text Definir si se desea convertir el texto en minusculas o mayuscula
     */
    public function __construct(private ?string $trasnform = null, private bool $IsNotEmpty = false)
    {
        
    }

    public function exec($val): string
    {
        if (!is_string($val)){
            throw new Error('Le valor ingrsado debe ser un string');
        }
        
        $pattern = '/^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/';
        if (preg_match($pattern, $val) >= 0){

            if ($this->trasnform === 'lowercase'){
                $val = strtolower($val);
            } else if ($this->trasnform === 'uppercase'){
                $val = strtoupper($val);
            }

            if ($this->IsNotEmpty && empty($val)){
                throw new ThrowError('El valor no puede estar vacio');
            }

            return $val;
        } else {
            throw new Error("El correo electronido debe ser valido $val");
        }
    }
}