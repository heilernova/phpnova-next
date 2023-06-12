<?php
namespace Phpnova\Next\Factory;

use Phpnova\Next\APIConfig;
use Phpnova\Next\App;
use Phpnova\Next\Config\Databases;
use Phpnova\Next\Routing\Router;
use Phpnova\Next\Utils\jwt;
use ReflectionClass;
use Symfony\Component\Yaml\Yaml;

class AppFactory
{
    public static function create(?string $dir = null, bool $enablePanel = false)
    {
        # Directorio princial
        $appConfigPath = "$dir/app.yaml";
    
        # Si no existe el archivo se crea una configuración por defecto
        if (!file_exists($appConfigPath)){
            $stream = self::getConfigDefault();
            file_put_contents($appConfigPath, Yaml::dump($stream, indent: 2, flags: Yaml::PARSE_OBJECT_FOR_MAP));
        }

        $object = Yaml::parse(file_get_contents($appConfigPath));

        if ($enablePanel){
            Router::use('nv-panel', fn() => require __DIR__ . '/../Panel/panel-router.php');
        }

        $config = new APIConfig();
        $reflection = new ReflectionClass($config);
        $reflection->getProperty("dir")->setValue($config, $dir);
        $reflection->getProperty("data")->setValue($config, $object);
        $reflection->getProperty("databases")->setValue($config, new Databases($object['databases'] ?? [], $config));
        $reflection->getProperty("environments")->setValue($config, new Databases($object['environments'] ?? [], $config));

        $reflection = new ReflectionClass(jwt::class);
        $reflection->setStaticPropertyValue('key', $config->getPrivateKey('jwt'));

        date_default_timezone_set($config->getTimezone());

        $app = new App();
        $reflection = new ReflectionClass($app);

        $reflection->getProperty("config")->setValue($app, $config);

        return $app;
    }

    private static function getConfigDefault(): array
    {
        return [
            "version" => "1.0.0.BETA",
            "timezone" => "UTC",
            "debug" => true,
            "private_keys" => [
                "jwt" => bin2hex(openssl_random_pseudo_bytes(25))
            ],
            "user" => [
                "username" => "admin",
                "email" => null,
                "password" => password_hash("admin", PASSWORD_DEFAULT, ["cos" => 3])
            ]
        ];
    }
}