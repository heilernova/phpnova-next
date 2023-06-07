<?php
namespace Phpnova\Next\Http\Trasnform;

use Attribute;

#[Attribute()]
class Trasnform
{
    private $function;

    public function exec($val){
        $fun = $this->function;
        return $fun($val);
    }
}