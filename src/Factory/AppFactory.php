<?php
namespace Phpnova\Next\Factory;

use Phpnova\Next\App;
use Phpnova\Next\Config;
use Phpnova\Next\Routing\Router;
use ReflectionClass;
use Symfony\Component\Yaml\Yaml;

use function PHPSTORM_META\type;

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
        // echo gettype($object); exit;

        if ($enablePanel){
            // require __DIR__ . '/../Panel/router.php';
            Router::use('nv-panel', fn() => require __DIR__ . '/../Panel/router.php');
        }

        $config = new Config();
        $reflection = new ReflectionClass($config);
        $reflection->getProperty("dir")->setValue($config, $dir);
        $reflection->getProperty("data")->setValue($config, $object);

        $app = new App();
        $reflection = new ReflectionClass($app);

        // echo gettype($config); exit;
        $reflection->getProperty("config")->setValue($app, $config);

        return $app;
    }

    private static function getConfigDefault()
    {
        return [
            "version" => "1.0.0.BETA",
            "timezone" => "+00:00",
            "debug" => true,
            "private_keys" => [
                "jwt" => bin2hex(openssl_random_pseudo_bytes(25))
            ]
        
        ];
    }
}