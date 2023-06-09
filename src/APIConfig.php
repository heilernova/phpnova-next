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