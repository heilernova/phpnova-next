<?php
namespace Phpnova\NExt\Http;

use Phpnova\Rest\AppConfig;
use SplFileInfo;

class FileData
{
    /** Nombre del archivo */
    public readonly string $name;
    /** Tipo de archivo
     * @example - image/jpg, image/png
     */
    public readonly string $type;
    /** Ruta temporal del archivo */
    public readonly string $tmpName;
    /** Tamaño del archivo */
    public readonly int $size;
    public readonly mixed $error;
    /** Extensión del archivo
     * @example - jpg, pgn, zip, pdf, etc. 
     */
    public readonly string $extension;

    public function __construct(array $data)
    {
        $this->name = $data['name'];
        $this->type = $data['type'];
        $this->tmpName = $data['tmp_name'];
        $this->size = $data['size'];

        $file_info = new SplFileInfo($this->name);
        $this->extension = $file_info->getExtension();
    }

    /**
     * Guarda el archivo en el servidor.
     * @param string|null $name Nombre del archivo, no es nesesario colocar la extensión
     * @return string|false Retorna la ruta completa del archivo, o false
     * en caso de ponerse guardar el archivo
     * @example -
     * * Guardar con nombre personalizado: `$file->save("nombre")`, se guardara en la raiz del proyecto
     *  a menos que especifique el directorio `$file->save("direction/nombre")`
     * * Guardar en un directorio: `$file->save("directorio/")`, el nombre del archivo
     * sera el que original
     */
    public function save(?string $name = null): string|false
    {
        $dir = ''; #AppConfig::getDir();
        $file_name = "";
        $file_extencion = $this->extension;
        $full_name = "";    

        if ($name){
            $explode = explode('/', ltrim($name, '/'));
            $name_temp = "";
            if (!str_ends_with($name, '/')){
                $name_temp = array_pop($explode);
                // $file
            }
            $temp = $dir;
            
            foreach($explode as $n){
                $temp .= "/$n";
                $file_name .= "/$n";
                if (!file_exists($temp)){
                    mkdir($temp);
                }
            }
            $temp = substr($temp, strlen($dir));

            // echo $temp; exit;
            if ($name_temp) $file_name = "$temp/$name_temp";
            
        } else {
            $file_name = $this->name;
        }
        
        $full_name = "$dir" . "$file_name.$file_extencion";

        $method = $_SERVER['REQUEST_METHOD'];
        if ($method == "POST" || $method == "GET") {
            return move_uploaded_file($this->tmpName, $full_name) ? $full_name : false;
        } else {
            return rename($this->tmpName, $full_name) ? $full_name : false;
        }
        return false;
    }
}