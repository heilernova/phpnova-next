<?php
namespace Phpnova\Next;

use Error;
use Phpnova\Next\Config\Databases;
use Symfony\Component\Yaml\Yaml;

class APIConfig
{
    private array $data = [];
    private string $dir = "";
    private Databases $databases;

    public function getVersion(): string
    {
        return $this->data["version"];
    }

    public function get(string $name): mixed
    {
        if (!array_key_exists($name, $this->data)){
            throw new ThrowError("No se encontro la propiedad $name en el app.yaml");
        }
        return $this->data[$name];
    }

    /**
     * Retorna el directorio donde se ejucta la aplicación
     */
    public function getDir(): string
    {
        return $this->dir;
    }

    public function getTimezone(): string
    {
        return $this->data["timezone"];;
    }

    public function isDebug(): bool
    {
        return $this->data["debug"] ?? false;
    }

    public function getPrivateKey(string $key): string
    {
        try {
            return $this->data["private_keys"][$key];
        } catch (\Throwable $th) {
            throw new Error("No se encontro la clave privada [$key]");
        }
    }

    public function getUser(): array
    {
        if (!array_key_exists("user", $this->data)){
           throw new ThrowError("No se encontro la información del usuario de acceso");
        }
        return $this->data["user"];
    }

    public function setUser(string $username, string $email, string $password): void
    {
        $this->data["user"] = [
            "username" => $username,
            "email" => $email,
            "password" => $password
        ];
    }

    public function getDatabases()
    {
        return $this->databases;
    }

    public function update(array $options): void
    {
        foreach($options as $key => $val){
            $this->data[$key] = $val;
        }
        $this->save();
    }

    public function save(): void
    {
        file_put_contents($this->dir . "/app.yaml", Yaml::dump($this->data, indent: 2));
    }
}