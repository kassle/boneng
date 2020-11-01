<?php declare(strict_types=1);

namespace Boneng;

class Response {
    private $statusCode;
    private $headers;
    private $body;

    public function __construct(int $statusCode, array $headers, string $body) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
    }

    public function getStatusCode() : int {
        return $this->statusCode;
    }

    public function getHeaders() : array {
        return $this->headers;
    }

    public function getBody() : string {
        return $this->body;
    }
}