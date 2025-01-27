<?php

namespace App\Utils;

class Request
{
    public array $query = [];
    public array $request = [];
    public array $attributes = [];
    private string $pathInfo;
    private string $method;
    private string $content;

    public function __construct(array $query = [], array $request = [], array $attributes = [], string $pathInfo = '',string $method = '', string $content = '')
    {
        $this->query = $query;
        $this->request = $request;
        $this->attributes = $attributes;
        $this->pathInfo = $pathInfo;
        $this->method = $method;
        $this->content = $content;
    }

    public static function createFromGlobals(): self
    {
        $query = $_GET ?? [];
        $request = $_POST ?? [];
        $pathInfo = $_SERVER['PATH_INFO'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $content = file_get_contents('php://input');


        return new self($query, $request,[], $pathInfo, $method, $content);
    }

    public function getPathInfo(): string
    {
        return $this->pathInfo;
    }
    public function getMethod(): string
    {
        return $this->method;
    }
    public function getContent(): string
    {
        return $this->content;
    }
    public function get(string $key, $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }
    public function setAttribute(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
    }
    public function add(array $data): void
    {
        $this->attributes = array_merge($this->attributes, $data);
    }
    public function query(): array
    {
        return $this->query;
    }

}