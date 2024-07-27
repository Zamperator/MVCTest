<?php

namespace App\Lib;

use App\Models\UserModel;

class Request
{
    protected array $get;
    protected array $post;
    protected array $request;
    protected array $cookies;
    protected array $server;

    public function __construct()
    {
        $this->get = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->request = $_REQUEST ?? [];
        $this->server = $_SERVER ?? [];
        $this->cookies = $_COOKIE ?? [];
    }

    // Get a value from the GET parameters with optional type checking
    public function get($key = null, $default = null, $type = 'string')
    {
        return $this->getValue($this->get, $key, $default, $type);
    }

    // Get a value from the POST parameters with optional type checking
    public function post($key = null, $default = null, $type = 'string')
    {
        return $this->getValue($this->post, $key, $default, $type);
    }

    // Get a value from either GET or POST parameters with optional type checking
    public function input($key = null, $default = null, $type = 'string')
    {
        return $this->getValue($this->request, $key, $default, $type);
    }

    public function cookies($key = null, $default = null, $type = 'string')
    {
        return $this->getValue($this->cookies, $key, $default, $type);
    }

    public function requests(): array
    {
        return $this->request;
    }

    // Get a value from the SERVER parameters
    public function server($key = null, $default = null)
    {
        if ($key === null) {
            return $this->server;
        }

        return $this->server[$key] ?? $default;
    }

    // General method for getting a value with type checking
    protected function getValue($source, $key, $default, $type)
    {
        if ($key === null) {
            return $source;
        }

        $value = $source[$key] ?? $default;

        switch ($type) {
            case 'int':
                return filter_var($value, FILTER_VALIDATE_INT) !== false ? (int)$value : (int)$default;
            case 'float':
                return filter_var($value, FILTER_VALIDATE_FLOAT) !== false ? (float)$value : (float)$default;
            case 'bool':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) !== null ? (bool)$value : (bool)$default;
            default:
                return trim((string)$value);
        }
    }

    // Check if the request is a POST request
    public function isPost(): bool
    {
        return $this->server('REQUEST_METHOD') === 'POST';
    }

    // Check if the request is a GET request
    public function isGet(): bool
    {
        return $this->server('REQUEST_METHOD') === 'GET';
    }

    public function isAjax(): bool
    {
        return isset($this->server['HTTP_X_REQUESTED_WITH']) && $this->server['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    // Get the request method
    public function method()
    {
        return $this->server('REQUEST_METHOD');
    }

    // Get the request URI
    public function uri()
    {
        return $this->server('REQUEST_URI');
    }

    public function path()
    {
        return $this->server('REQUEST_PATH');
    }

    public function referrer()
    {
        return $this->server('HTTP_REFERER') ?? '';
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}