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

### app.yaml

```yaml
version: 1.0.0
timezone: '+00:00'
debug: true
private_keys:
  jwt: eb52e801e49bb9522ae64ab57bdaae18dc2f525bd31b7bc0f8
```# phpnova-next
