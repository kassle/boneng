<?php declare(strict_types=1);

namespace Boneng\Model;

class Request {
    private $method;
    private $path;
    private $headers;
    private $params;
    private $body;

    public function __construct(string $method, string $path, array $headers, array $params, array $body) {
        $this->method = $method;
        $this->path = $path;
        $this->headers = $headers;
        $this->params = $params;
        $this->body = $body;
    }

    public function getMethod() : string {
        return $this->method;
    }

    public function getPath() : string {
        return $this->path;
    }

    public function getHeaders() : array {
        return $this->headers;
    }

    public function getParameters() : array {
        return $this->params;
    }

    public function getBody() : array {
        return $this->body;
    }
}