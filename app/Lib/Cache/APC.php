<?php

namespace App\Lib\Cache;

use App\Lib\Utils;
use Exception;

/**
 *
 */
class APC extends Factory implements Methods
{

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();

        if (extension_loaded('apc')) {
            throw new Exception('APC not available');
        }
    }

    /**
     * @param string $cacheKey
     * @return bool
     */
    public function delete(string $cacheKey): bool
    {
        $cacheKey = $this->cleanUpKey($cacheKey);

        if (apcu_exists($cacheKey)) {
            return apcu_delete($cacheKey);
        }

        return false;
    }

    /**
     * @param string $cacheKey
     * @return mixed
     */
    public function get(string $cacheKey): mixed
    {

        if ($this->clear($cacheKey, $this)) {
            return '';
        }

        return apcu_exists($cacheKey) ? apcu_fetch($this->cleanUpKey($cacheKey)) : '';
    }

    /**
     * @param string $cacheKey
     * @param mixed $insertValue
     * @param int $expiresIn
     * @return void
     */
    public function set(string $cacheKey, mixed $insertValue, int $expiresIn = 600): void
    {

        $cacheKey = $this->cleanUpKey($cacheKey);

        if (is_object($insertValue)) {
            die('Objects are not allowed to be stored in memcache');
        }

        $insertValue = (is_array($insertValue))
            ? $this->cleanUpRecursive($insertValue)
            : Utils::cleanup($insertValue);

        apcu_add($cacheKey, $insertValue, $expiresIn);
    }
}
