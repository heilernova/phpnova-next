<?php
namespace Phpnova\Next\Http;

use Error;
use Phpnova\Next\Http\Validators\IsEmail;
use Phpnova\Next\Http\Validators\IsUUID;
use Phpnova\Next\Http\Validators\Validator;
use Phpnova\Next\ThrowError;
use ReflectionClass;
use Throwable;

class BodyValid
{
    public static function parce(object $body, string|object $class)
    {
        try {
            $class = is_string($class) ? new $class() : $class; 
            $reflection = new ReflectionClass($class);
            $errors = [];
    
            # Recorremos las propiedades de la clase
            foreach($reflection->getProperties() as $property){
                $name = $property->getName();
                $type = $property->getType();
                $valInit = null; 

                if (isset($body->$name)){
                    # Si al propiedad existe
                    $val = $body->$name;
                    $valfinal = null;

                    # Validamos los atributos del parametro
                    foreach($property->getAttributes() as $att){
                        $attribute = $att->newInstance();
                        if ($attribute instanceof Validator){
                            try {
                                $valfinal = $attribute->exec($val);
                            } catch (\Throwable $th) {
                                if ($valfinal instanceof Throwable){
                                    $valfinal = new Error($valfinal->getMessage() . "\n-" . $th->getMessage());
                                } else {
                                    $valfinal = new Error('- ' . $th->getMessage());
                                }
                            }
                        }
                    }

                    if ($valfinal instanceof Throwable){
                        $errors[] = [
                            'name' => $name,
                            'message' => $valfinal->getMessage()
                        ];
                        continue;
                    }

                    try {
                        $property->setValue($class, $valfinal);
                    } catch (\Throwable $th) {
                        $errors[] = [
                            'name' => $name,
                            'message' => "" . $th->getMessage() . " value: " . json_encode($valfinal)
                        ];
                    }

                } else {
                    # Si la propiedad no existe
                    if ($property->isDefault()){
                        $property->setValue($class, $property->getDefaultValue());
                    } else {
                        $errors[] = [
                            'name' => $name,
                            'message' => "Se requiere la propiedad [$name]"
                        ];
                    }
                }
            }

            if ($errors){
                $msg = "";
                foreach($errors as $err) {
                    $msg .= '[ ' . $err['name'] . " ]\n" . $err['message'] . "\n\n"; 
                }
                throw new ThrowError("error de parametros \n\n$msg");
            }

            return $class;

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}