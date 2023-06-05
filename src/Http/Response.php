<?php
namespace Phpnova\Next\Http;

class Response
{
    private array $data = [];
    private string $type;
    private mixed $body;
    private int $status;


    public function __construct(mixed $data, int $status = 200, string $type = 'json')
    {
        $this->type = $type;
        $this->body = $data;
        $this->status = $status;
    }

    public function set(string $key, mixed $value): Response
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function get(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }


    public function addHeader(string $name, string $content): void
    {

    }

    public function getBody(): mixed {
        return $this->body;
    }

    public function setBody(mixed $data): void {
        $this->body = $data;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public static function json($data, int $status = 200): Response
    {
        return new Response($data, $status, 'json');
    }

    public static function file(string $filename, int $status = 200, bool $autodelete = false): Response
    {
        return (new Response($filename, $status, 'file'))->set('auto_delete_file', $autodelete);
    }

    public static function sendStatus(int $status = 200): Response
    {
        return new Response(null, $status);
    }
}