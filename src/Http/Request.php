<?php
namespace Phpnova\Next\Http;

class Request
{
    public readonly string $url;
    public readonly array $headers;
    public readonly string $method;
    public array $params;
    public readonly array $queryParams;
    public readonly mixed $body;
    public readonly array $files;
}