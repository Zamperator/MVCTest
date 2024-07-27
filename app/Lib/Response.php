<?php

namespace App\Lib;

class Response
{
    protected int $statusCode = 200;
    protected array $headers = [];
    protected string $body = '';

    public function withStatus($statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function withHeader($name, $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function withBody($body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
        echo $this->getBody();
    }
}