<?php
namespace Phpnova\Next;

use Phpnova\Next\Http\Cors;
use Phpnova\Next\Http\HttpExeption;
use Phpnova\Next\Http\HttpFuns;
use Phpnova\Next\Http\Request;
use Phpnova\Next\Http\Response;
use Phpnova\Next\Routing\ControlRouter;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use SplFileInfo;

class App
{
    private Config $config;
    private array $routes = [];
    private Request $request;

    public function getConfig()
    {
        return $this->config;
    }

    public function addControllers(string $controller, string $prefix = null)
    {
        // $this->mappig[] = [
        //     "controller" => $controller,
        //     "prefix" => 
        // ];
    }

    public function get()
    {

    }

    public function post()
    {

    }

    public function delete()
    {
        
    }

    public function use(mixed ...$args)
    {
        $num = count($args);
        if ($num == 1){
            $arg = $args[0];
            if (is_string($arg)){
                $reflection = new ReflectionClass($arg);
                foreach ($reflection->getMethods() as $method){
                    $this->routes[] = [
                        "method" => "GET",
                        "action" => [$arg, $method->getName() ]
                    ];
                }
            }
        }
    }

    public function enableCors(): void
    {
        Cors::loadCors();
    }

    public function run(): never
    {
        try {
            $url = "/" . urldecode(explode( '?', trim(substr($_SERVER['REQUEST_URI'], strlen(dirname($_SERVER['SCRIPT_NAME']))), "/"))[0]) . "/";
            $method = $_SERVER['REQUEST_METHOD'];
            $response = new Response(null, 404);
            $actions = ControlRouter::run($url, $method);
            $this->request = new Request();
            $reflection_request = new ReflectionClass($this->request);
            $reflection_request->getProperty('url')->setValue($this->request, $url);
            $reflection_request->getProperty('method')->setValue($this->request, $method);
            $reflection_request->getProperty('headers')->setValue($this->request, apache_request_headers());
    
            $contentBody = HttpFuns::getContentRequest();
    
            if ($contentBody){
                $reflection_request->getProperty("body")->setValue($this->request, $contentBody['body'] ?? null);
                $reflection_request->getProperty("files")->setValue($this->request, $contentBody['files'] ?? []);
            } else {
                $reflection_request->getProperty("body")->setValue($this->request, null);
                $reflection_request->getProperty("files")->setValue($this->request, []);
            }
    
            foreach($actions as $action){
                if (is_callable($action)){
                    $response = $action();
                    if (is_null($response)){
                        continue;
                    }
                    break;
                } else {
                    $response = $this->exceuteAction($action);
                }
            }
        } catch (HttpExeption $httpExeption) {

        } catch (\Throwable $th) {
            $log = "[]";
            $response = new Response([
                'message' => $th->getMessage()
            ], 500);
        }

        if (!($response instanceof Response)) {
            $response = new Response($response);
        }

        $reflection = new ReflectionClass($response);
        
        $type = $reflection->getProperty('type')->getValue($response);
        $status = $reflection->getProperty('status')->getValue($response);
        $body = $reflection->getProperty('body')->getValue($response); #$body = $route;

        $content_type = match($type){ 
            'json' => 'application/json',
            'html' => 'text/html',
            'text-plain' => 'text-plain',
            'file' => HttpFuns::getContentType((new SplFileInfo($body))->getExtension())
        };

        $body = match($type) {
            'json' => json_encode($body, 128),
            'text-plain' => $body,
            'file' => file_get_contents($body),
            'html' => $body
        };

        header("Content-Type: $content_type");
        echo $body;
        http_response_code($status);
        
        exit();
    }

    private function exceuteAction(array $route)
    {
        $action = $route["action"];
        if (is_callable($action)){
            $reflection_fucntion = new ReflectionFunction($action);
            $params = $this->reflectionActionParams($reflection_fucntion);
            $response = $reflection_fucntion->invokeArgs($params);
        } else if (is_array($action)) {
            try {
                $controller = new $action[0]();
                $controller_reflection = new ReflectionClass($controller);
                $method = $controller_reflection->getMethod($action[1]);
                $params = $this->reflectionActionParams($method);
                $response = $method->invokeArgs($controller, $params);
            } catch (\Throwable $th) {
                throw $th;
            }
        } else if (is_string($action)){
            # Si es un string, el namespace de un controlador
        }
        return $response;
    }

    private function reflectionActionParams(ReflectionMethod|ReflectionFunction $action): array
    {
        $params = [];
        foreach ($action->getParameters() as $param) {
            $type = $param->getType();
            if ($type && !$param->getType()->isBuiltin()){
                $class = $param->getType()->getName();
                if ($class == Config::class){
                    $params[] = $this->config;
                } else if ($class == Request::class){
                    $params[] = $this->request;
                }
                continue;
            }
        }
        return $params;
    }
}