<?php
namespace Phpnova\Next\Routing;

use Error;
use Exception;
use Phpnova\Next\ThrowError;
use ReflectionClass;

class Router
{
    public static function use(...$arg): void
    {
        try {
            $count = count($arg);

            switch ($count) {
                case 1:
                    if (!is_callable($arg[0])) throw new Exception("El argumento ingresado debe ser una función");
                    ControlRouter::add($arg[0]);
                    break;

                case 2:
                    if (is_string($arg[0]) && (is_callable($arg[1]) || is_string($arg[1]))){
                        ControlRouter::add([
                            'path' => $arg[0],
                            'type' => 'router',
                            'action' => $arg[1]
                        ]);
                    }
                
                default:
                    # code...
                    break;
            }
        } catch (\Throwable $th) {
            throw new ThrowError($th);
        }
    }

    public static function get(string $path, callable|array $action): void
    {
        ControlRouter::add([
            'path' => $path,
            'method' => 'GET',
            'type' => 'controller',
            'action' => $action
        ]);
    }

    public static function post(string $path, callable|array $action): void
    {
        ControlRouter::add([
            'path' => $path,
            'method' => 'POST',
            'type' => 'controller',
            'action' => $action
        ]);
    }

    public static function put(string $path, callable|array $action): void
    {
        ControlRouter::add([
            'path' => $path,
            'method' => 'PUT',
            'type' => 'controller',
            'action' => $action
        ]);
    }

    public static function delete(string $path, callable|array $action): void
    {
        ControlRouter::add([
            'path' => $path,
            'method' => 'DELETE',
            'type' => 'controller',
            'action' => $action
        ]);
    }

    public static function pacth(string $path, callable|array $action): void
    {
        ControlRouter::add([
            'path' => $path,
            'method' => 'PACTH',
            'type' => 'controller',
            'action' => $action
        ]);
    }
}