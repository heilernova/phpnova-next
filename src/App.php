<?php
namespace Phpnova\Next;

use Closure;
use DateTime;
use Phpnova\Next\Http\Attributes\Body;
use Phpnova\Next\Http\Attributes\Files;
use Phpnova\Next\Http\BodyValid;
use Phpnova\Next\Http\Cors;
use Phpnova\Next\Http\HttpExeption;
use Phpnova\Next\Http\HttpFuns;
use Phpnova\Next\Http\Request;
use Phpnova\Next\Http\Response;
use Phpnova\Next\Routing\ControlRouter;
use Phpnova\Next\Routing\Router;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use SplFileInfo;

class App
{
    private APIConfig $config;
    private array $routes = [];
    private Request $request;
    
    /** @var (\Closure(Response $res): Response )|null */
    private mixed $handleResponse = null;
    
    /** @var (\Closure(Response $res): Response )|null */
    private mixed $handleHttpException = null;
    
    /** @var (\Closure(\Throwable $res): Response )|null */
    private mixed $handleExeption = null;

    public function getConfig(): APIConfig
    {
        return $this->config;
    }

    /**
     * @param (\Closure(Response $res): Response ) $funtion
     */
    public function handleResponse(Closure $funtion): void
    {
        $this->handleResponse = $funtion;
    }

    /**
     * @param (\Closure(Response $res): Response ) $funtion
     */
    public function handleHttpException(Closure $function): void
    {
        $this->handleHttpException = $function;
    }
    /**
     * @param (\Closure(\Throwable $res): Response ) $funtion
     */
    public function handleException(Closure $function): void
    {
        $this->handleExeption = $function;
    }

    public function use(mixed ...$args): void
    {
        Router::use(...$args);
    }

    public function enableCors(): void
    {
        Cors::loadCors('*', '*', '*');
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
    
            $contentBody = HttpFuns::getContentRequest($this->config->getDir());
    
            if ($contentBody){
                $reflection_request->getProperty("body")->setValue($this->request, $contentBody['body'] ?? null);
                $reflection_request->getProperty("files")->setValue($this->request, $contentBody['files'] ?? []);
            } else {
                $reflection_request->getProperty("body")->setValue($this->request, null);
                $reflection_request->getProperty("files")->setValue($this->request, []);
            }
    
            $reflection_request->getProperty("queryParams")->setValue($this->request, $_GET);

            foreach($actions as $action){
                $reflection_request->getProperty("params")->setValue($this->request, $action['params'] ?? []);
                if (is_callable($action)){
                    $response = $action();
                    if (is_null($response)){
                        continue;
                    }
                    break;
                } else {
                    $reflection_request->getProperty("params")->setValue($this->request, $action['params'] ?? []);
                    $response = $this->exceuteAction($action);
                }
            }

            if (!($response instanceof Response)) {
                $response = new Response($response);
            }    

            # Manejamos la respuseta si existe
            if ($this->handleResponse){
                $fn = $this->handleResponse;
                $response = $fn($response);
            }

            $this->logRequest($response);
        } catch (HttpExeption $httpExeption) {

            $response = new Response( $httpExeption->getMessage(), $httpExeption->getCode());
            if ($this->handleHttpException){
                $fn = $this->handleHttpException;
                $response = $fn($response);
            }
            $this->logRequest($response);
        } catch (\Throwable $th) {
            $log = "[]";
            $jsonResponse = [
                'message' => $th->getMessage(),
                'error' => [
                    'line' => $th->getLine(),
                    'file' => $th->getFile()
                ]
            ];
            
            $this->logRequest(new Response($jsonResponse, 500));

            if (!$this->config->isDebug()){
                $jsonResponse = [
                    'message' => 'Error interno del servidor',
                    'error' => 'Server errror'
                ];
            }
            $response = new Response($jsonResponse, 500);
        }

        $reflection = new ReflectionClass($response);
        
        $type = $reflection->getProperty('type')->getValue($response);
        $status = $reflection->getProperty('status')->getValue($response);
        $body = $reflection->getProperty('body')->getValue($response);

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

    private function exceuteAction(array $route): mixed
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
            throw new ThrowError("No se definido la accion del código base");
        }
        return $response;
    }

    private function reflectionActionParams(ReflectionMethod|ReflectionFunction $action): array
    {
        $params = [];
        foreach ($action->getParameters() as $param) {
            $type = $param->getType();
            # En caso de que sea una clase
            if ($type && !$param->getType()->isBuiltin()){
                $class = $param->getType()->getName();
                if ($class == APIConfig::class){
                    $params[] = $this->config;
                } else if ($class == Request::class){
                    $params[] = $this->request;
                } else {
                    $object = new $class();
                    $reflectionObject = new ReflectionClass($object);
                    foreach ($reflectionObject->getAttributes() as $classAttr){
                        if ($classAttr->getName() == Body::class){
                            $body = $this->request->body;
                            try {
                                $object = BodyValid::parce($body, $object);
                            } catch (\Throwable $th) {
                                throw new HttpExeption($th->getMessage(), 400);
                            }
                        }
                    }          
                    $params[] = $object;
                }
                continue;
            } else {
                # Es caso de ser un parametro nativo

                # Recorremos los atributos de los parametros
                $temp = null;
                foreach($param->getAttributes() as $att){
                    if ($att->getName() == Body::class){
                        $temp = $this->request->body;
                        break;
                    }

                    if ($att->getName() == Files::class){
                        $temp = $this->request->files;
                        break;
                    }
                }

                if ($temp){
                    $params[] = $temp;
                } else {
                    if (array_key_exists($param->getName(), $this->request->params)){
                        $params[] = $this->request->params[$param->getName()];
                    } else {
                        if ($param->isOptional()){
                            $param[] = $param->getDefaultValue();
                        } else {
                            throw new ThrowError("Falta parametros en la consulta");
                        }
                    }
                }
            }
        }
        return $params;
    }

    public function logRequest(Response $response): void
    {
        $dir = $this->config->getDir();
        if (!file_exists("$dir/logs")) mkdir("$dir/logs");

        $objectDate = new DateTime();
        $date = $objectDate->format('Y-m-d H:i:s P');
        $method = str_pad($this->request->method, 6, ' ');
        $url = $this->request->url;
        $body = base64_encode(json_encode($this->request->body));
        $status = $response->getStatus();
        $data = "[$date] $method $status '$url' $body\n";
        $stream = fopen("$dir/logs/requests.log", 'a+');
        fputs($stream, $data);
        fclose($stream);
    }

    public function logError(\Throwable $th): void
    {
        $dir = $this->config->getDir();
        if (!file_exists("$dir/logs")) mkdir("$dir/logs");
        $data =  "[]";
        file_put_contents("$dir/logs/errors.log", $data);
    }
}