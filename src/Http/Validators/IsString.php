<?php
namespace Phpnova\Next\Http\Validators;

use Attribute;
use Phpnova\Next\ThrowError;

use function PHPSTORM_META\type;

#[Attribute()]
class IsString extends Validator
{
    /**
     * @param 'lowercase' | 'uppercase' | 'capitalize' | null  $transform
     */
    public function __construct(private $transform = null, private bool $IsNotEmpty = true)
    {
        
    }

    public function exec($val)
    {
        if (is_string($val)) {
            if ($this->IsNotEmpty && empty($val)){
                throw new ThrowError('El valor no puede estar vacio');
            }
            switch($this->transform){
                case 'lowercase':
                    $val = mb_strtolower($val);
                    break;
                case 'uppercase':
                    $val = mb_strtoupper($val);
                    break;
                case 'capitalize':
                    $val = ucwords(mb_strtolower($val));
                    break;
                default:
            }
            return $val;
        }
        else throw new ThrowError('El parametro debe ser un string');
    }
}