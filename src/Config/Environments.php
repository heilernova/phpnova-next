<?php
namespace Phpnova\Next\Config;

use Phpnova\Next\APIConfig;
use Phpnova\Next\ThrowError;

class Environments
{
    public function __construct(private array $environments,private APIConfig $config)
    {

    }

    public function getAll(): array
    {
        return $this->environments;
    }

    public function get(string $name): mixed
    {
        if (!array_key_exists($name, $this->environments)){
            throw new ThrowError("No se encontro la variable");
        }

        return $this->environments[$name];
    }

    public function set($name, $value): void
    {
        $this->environments[$name] = $value;
        $this->config->update(['environments' => $this->environments]);
    }
}