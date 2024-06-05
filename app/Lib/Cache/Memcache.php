<?php
/** @noinspection PhpUnused */

namespace App\Lib\Cache;

use Exception;
use Memcached;

/**
 * @uses Factory
 * @uses Methods
 * @package App\Lib\Cache
 * @version 1.0
 * @since 1.0
 * @see https://www.php.net/manual/de/book.memcached.php
 */
class Memcache extends Factory implements Methods
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        if (!extension_loaded('memcached')) {
            throw new Exception('Memcached not available');
        }

        $config = $this->config['cache']['classes']['Memcached'];

        $this->instance = new Memcached();
        $this->instance->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
        $this->instance->setOption(Memcached::OPT_COMPRESSION, false);

        $this->instance->addServers($config['servers']);
    }

    public function delete(string $cacheKey): bool
    {
        return $this->instance->delete($cacheKey);
    }

    public function get(string $cacheKey): mixed
    {
        return $this->instance->get($cacheKey);
    }

    public function set(string $cacheKey, mixed $insertValue, int $expiresIn = 0): void
    {
        $this->instance->set($cacheKey, $insertValue, $expiresIn);
    }
}
