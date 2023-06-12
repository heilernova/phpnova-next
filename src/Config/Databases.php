<?php
namespace Phpnova\Next\Config;

use Phpnova\Next\APIConfig;
use Phpnova\Next\Config;
use Phpnova\Next\ThrowError;

class Databases
{
    public function __construct(private array $data,private APIConfig $config)
    {

    }

    public function get(string $name = null)
    {
        if ($name && count($this->data) > 0) return $this->data[0];
        $index = array_search($name, array_column($this->data, 'name'));
        if ($index) return $this->data[$index];
        throw new ThrowError("No se encontrol la configuración de la base de datos");
    }

    public function getAll(): array
    {
        return $this->data;
    }

    public function add(string $name, string $type, string $host, string $user, string $password, string $database, int $port = null)
    {
        $db = $this->data ?? [];
        $db[] = ['name' => $name, 'type' => $type, 'host' => $host, 'user' => $user, 'password' => $password, 'database' => $database, 'port' => $port];
        $this->config->update(['databases' => $db]);
    }
}