<?php
namespace Phpnova\Next\Routing\Attributes;

use Attribute;

#[Attribute]
class Delete extends Route
{
    public function __construct(string $path)
    {
        parent::__construct($path);
    }
}