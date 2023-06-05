<?php

namespace Phpnova\Next\Routing;

// use Phpnova\Rest\Http\Response;
use Phpnova\Next\Routing\Attributes\Route;
use ReflectionClass;

class ControlRouter
{
    private static array $routes = [];
    private static int $index = -1;
    private static int $indexTemp = 0;

    public static function add(mixed $dataRouter): void
    {
        if (self::$index == -1){
            if (is_array($dataRouter)){
                $dataRouter['path'] = '/' . trim($dataRouter['path'], '/') . '/';
                $dataRouter['path_key'] = self::generatePathKey($dataRouter['path']);
                self::$routes[] = $dataRouter;
            } else {
                self::$routes[] = $dataRouter;
            }
        } else {
            if (is_array($dataRouter)){
                $path_parent = trim(self::$routes[self::$index]['path'], '/');
                if ($path_parent) $path_parent = "/$path_parent";
                $path = trim($dataRouter['path'], '/');
                if ($path) $path .= '/';
                $dataRouter['path'] = "$path_parent/$path";
                $dataRouter['path_key'] = self::generatePathKey($dataRouter['path']);
                array_splice(self::$routes, self::$index + 1 + self::$indexTemp, 0, [$dataRouter]);
                self::$indexTemp++;
            } else {
                array_splice(self::$routes, self::$index + 1 + self::$indexTemp, 0, $dataRouter);
                self::$indexTemp++;
            }
        }
    }

    private static function generatePathKey(string $path): string
    {
        $patterns[] = "/(:\w+)/i";
        $replacements[] = ':p';
        return preg_replace($patterns, $replacements, $path);
    }

    private static function nextIndex(): int
    {
        self::$index = self::$index + 1;
        self::$indexTemp = 0;
        return self::$index;
    }

    private static function executeRouter(callable|string $action): void
    {
        if (is_callable($action)){
            $action();
            return;
        }

        $reflection = new ReflectionClass($action);
        foreach ($reflection->getMethods() as $method){
            foreach($method->getAttributes() as $att){

                $obj = $att->newInstance();
                
                if ($obj instanceof Route){
                
                    switch ($obj->method) {
                        case 'GET':
                            Router::get($obj->path, [$action, $method->getName() ]);
                            break;
                        case 'POST':
                            Router::post($obj->path, [$action, $method->getName()]);
                            break;
                        case 'PUT':
                            Router::put($obj->path, [$action, $method->getName()]);
                            break;
                        case 'PACTH':
                            Router::pacth($obj->path, [$action, $method->getName()]);
                            break;
                        case 'DELETE':
                            Router::delete($obj->path, [$action, $method->getName()]);
                        default:
                            # code...
                            break;
                    }
                }
            }
        }
    }

    /**
     * 
     */
    public static function run(string $url, string $method): array
    {
        $index = -1;
        $actions = [];

        while ($index !== null){
            $index++;

            $route = self::$routes[$index] ?? null;
            if ($route == null) {
                $index = null;
                continue;
            }

            self::nextIndex();
            if (is_callable($route)){
                # Falta agregarlos los parametros por reflection
                $actions[] = $route;
                continue;
            }

            $path = $route['path'] ?? null;
            $action = $route['action'];

            # Valiadsmo que la ruta cumpla con los criterios de path
            $patters = "/^" . str_replace(':p', '([^\/]+)', str_replace('/', '\/', $route['path_key']) ) . "$/i";
            if ($route['type'] == 'router') $patters = "/" . str_replace(':p', '(.+)', str_replace('/', '\/', $route['path_key']) ) . "/i";

            $match = preg_match($patters, $url);
            if ($match){
                switch ($route['type']) {
                    case 'router':
                        self::executeRouter($action);
                        break;
                    
                    case 'controller':
                    
                        if ($route['method'] == $method){
                            $explode_url = explode('/', $url);
                            $explode_path = explode('/', $route['path']);
                            $params = [];
                            for($i = 0; $i < count($explode_path); $i++){
                                $v = $explode_path[$i];
                                if (str_starts_with($v, ':')) $params[substr($v, 1)] = $explode_url[$i];
                            }

                            $route['params'] = $params;
                            $actions[] = $route;
                        }
    
                        break;
                    
                    default:
                        # code...
                        break;
                }
            } elseif ($route['path'] == '//' && $route['type'] == 'router') {
                self::executeRouter($action);
            }

        }
        // echo json_encode($actions, 128); exit;
        return $actions;
    }

}