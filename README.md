# Next API
Librería de PHP para el manejo de peticiones REST

## Requerimientos
* PHP 8.1^
* composer

Librerias de composer
* symfony/yaml: ^6.3
* firebase/php-jwt: ^6.5

## Instalación inicial
Para este projectilizaron en manejador de paquete de PHP Composer en ejecutando mendiante la consola en la raiz del proyecto
```
composer init
```
Instalamas las dependencias requeridos
```
composer require symfony/yaml firebase/php-jwt phpnova/next
```

## Configuracion inicial
### Estructura de archivo
```
└─src
  └─Config
    └─handle-response.php
    └─handle-exceptions.php
    └─handle-error.php
  └-rotuer.php #Aquí configuramos el enrutador
.htaccess
app.yaml
app.php
```

### app.yaml
El archvio app.yaml contiene la configuración inicial de la aplicación con se muestra en el siguiente ejemplo
```yaml
version: 1.0.0
timezone: 'UTC'
debug: true
private_keys:
  jwt: eb52e801e49bb9522ae64ab57bdaae18dc2f525bd31b7bc0f8
```

En caso de que deseamos agragar información extra para la conexión con la base de datos, lo agregamos de al siguiene manera al app.yaml
```yaml
databases:
  my_database:
    type: mysql
    host: localhost
    user: root
    password: my_passsword
    database: my_database
    port: 3306
```

Se prodra acceder a esta información desde la clase `Phpnova\Next\Config`

### app.php
En este fichero la entrada de la aplicación
```php
use App\Resources\Authentication\AuthenticationController;
use Phpnova\Next\Factory\AppFactory;
use Phpnova\Next\Http\Response;
use Phpnova\Next\Routing\Router;

require __DIR__ . '/vendor/autoload.php';

$app =  AppFactory::create(dir: __DIR__, enablePanel: true);
$app->use('/', function(){
  Router::get('', fn() => "Hola munod");
});
$app->run();
```

## Definir rutas de accesos
Para definir la rutas deacceso utilizares las clase `Phpnova\Next\Routing\Router`
```php
use Phpnova\Next\Routing\Router;

Router::get('/saludar', function(){
  return "Hola, ¿Como estas?";
});

# Utilizar parametro en la url
Router::get('/saludar/:name', function(string $name){
  return "Hola $name, ¿Como estas?";
});
```