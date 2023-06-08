<?php
namespace Phpnova\Next\Config;

use Phpnova\Next\Config;
use Phpnova\Next\ThrowError;

class Databases
{
    public function __construct(private array $data,private Config $config)
    {

    }

    public function get(string $name)
    {
        if (array_key_exists($name, $this->data ?? [])){
            return $this->data[$name];
        }
        throw new ThrowError("No se encontrol la configuración de la base de datos");
    }

    public function getAll(): array
    {
        $list = [];
        foreach ($this->data ?? [] as $key => $val){
            $list[] = [
                'name' => $key,
                ...$val
            ];
        }

        return $list;
    }

    public function add(string $name, string $type, string $host, string $user, string $password, string $database, int $port = null)
    {
        $db = $this->data ?? [];
        $db[$name] = [
            'type' => $type,
            'host' => $host,
            'user' => $user,
            'password' => $password,
            'database' => $database,
            'port' => $port
        ];

        $this->config->update(['databases' => $db]);
    }
}