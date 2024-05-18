<?php

namespace App\Lib\Cache;

use Exception;

/**
 * Class Redis
 */
class Redis extends Factory implements Methods
{
    private array $server;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        $this->server = $this->config['cache']['classes']['Redis']['server'];
    }

    /**
     * @return void
     * @throws Exception
     */
    private function connect(): void
    {
        try {
            $this->instance = fsockopen($this->server['host'], $this->server['port'], timeout: 10);
            if (!$this->instance) {
                die('Could not connect to Redis server');
            }
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function __wakeup()
    {
        $this->connect();
    }

    public function __destruct()
    {
        if ($this->instance) {
            fclose($this->instance);
        }
    }

    /**
     * @param string $cacheKey
     * @return bool
     */
    public function delete(string $cacheKey): bool
    {
        return (bool)fwrite($this->instance, "DEL $cacheKey\r\n");
    }

    /**
     * @param string $cacheKey
     * @return string|false
     */
    public function get(string $cacheKey): string|false
    {
        fwrite($this->instance, "GET $cacheKey\r\n");
        return fgets($this->instance);
    }

    /**
     * @param string $cacheKey
     * @param mixed $insertValue
     * @param int $expiresIn
     * @return void
     */
    public function set(string $cacheKey, mixed $insertValue, int $expiresIn = 0): void
    {
        fwrite($this->instance, "SET $cacheKey $insertValue\r\n");
    }
}
