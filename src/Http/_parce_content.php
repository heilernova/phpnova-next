<?php
/**
 * Autor: Heiler Nova <https://github.com/heilernova>
 * 
 * En esta sección nos encargaremos de procesar el contendio recivido por parte del cleinte
 */

use Phpnova\Next\Http\FileData;

$headers = apache_request_headers();
$content_type = explode(';', ($headers['Content-Type'] ?? $headers['content-type']) ?? '')[0] ?? '';

if (str_starts_with($content_type, 'application/json')){
    $body_content = file_get_contents("php://input");
    if ($body_content){
        // $request_data['body'] = json_decode($body_content);
        return [
            "body" =>  json_decode($body_content)
        ];
    }
    return null;
}

if (str_starts_with($content_type, 'multipart/form-data')){
    $request_data['body'] = $_POST;
    $request_data['files'] = $_FILES;
    foreach($_FILES as $key => $val) {
        $request_data['files'][$key] = new FileData($val);
    }

    return $request_data;
}

return null;