<?php
namespace Phpnova\Next;

use Error;
use Phpnova\Next\Config\Databases;
use Symfony\Component\Yaml\Yaml;

class Config
{
    private array $data = [];
    private string $dir = "";
    private Databases $databases;

    public function get(string $name): mixed
    {
        if (!array_key_exists($name, $this->data)){
            throw new ThrowError("No enctro la configuracion $name");
        }
        return $this->data[$name];
    }

    public function getVersion(): string
    {
        return $this->data["version"];
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

    public function isDebug()
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

    public function getUsers(): array
    {
        return $this->data["users"] ?? [];
    }

    public function addUser(string $name, string $email, string $password)
    {
        $this->data["users"][] = [
            "username" => strtolower($name),
            "email" => strtolower($email),
            "password" => password_hash($password, PASSWORD_DEFAULT, ['cos' => 3])
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