<?php
namespace Phpnova\Next\Http\Trasnform;

use Attribute;

#[Attribute()]
class LowerCase extends Trasnform
{
    private $function;
    public function __construct()
    {
        $this->function = function(string $text){
            return strtolower($text);
        };
    }
}