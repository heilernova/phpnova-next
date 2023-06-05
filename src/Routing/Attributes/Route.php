<?php

namespace Phpnova\Next\Routing\Attributes;

use Attribute;

#[Attribute]
class Route
{
    /**
     * @param string $path
     * @param 'GET'|'POST'|'PUT'|'PACTH'|'DELETE' $method
     */
    public function __construct(public string $path = '', public string $method = 'GET')
    {
        $this->method = strtoupper($method);
    }

    public function getRoutes(): void
    {
        
    }
}