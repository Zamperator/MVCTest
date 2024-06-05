<?php

namespace App\Lib\Cache;

use App\Lib\Registry;
use App\Lib\Utils;

/**
 * @uses Registry
 * @package App\Lib\Cache
 * @version 1.0
 * @since 1.0
 */
abstract class Factory
{

    protected mixed $instance;

    protected array $config;

    public function __construct()
    {
        $this->config = Registry::get('config');
    }

    /**
     * @param $toCleanup
     *
     * @return array
     */
    public function cleanUpRecursive($toCleanup): array
    {
        array_walk_recursive($toCleanup, 'Utils::cleanup');
        return $toCleanup;
    }

    /**
     * @param string $sCacheKey
     *
     * @return string
     */
    public function cleanUpKey(string $sCacheKey = ''): string
    {
        $sCacheKey = preg_replace('#[^a-z0-9_]#', '_', strtolower($sCacheKey));
        $sCacheKey = preg_replace('#_{2,}#', '_', $sCacheKey);

        return Utils::cleanup($sCacheKey);
    }

    /**
     * @param string $cacheKey
     * @param mixed|null $cacheObject
     * @return bool
     */
    protected function clear(string $cacheKey = '', mixed $cacheObject = null): bool
    {
        if (isset($_GET['cacheclear']) /* && some validation for Developer */
            && !empty($cacheKey)
            && is_object($cacheObject)
            && method_exists($cacheObject, 'delete')) {
            $user = Registry::get('user');
            if ((int)$user->user_id && $user->user_id == $this->config['admin'][$user->login_type]) {
                $cacheObject->delete($cacheKey);

                return true;
            }
        }

        return false;
    }
}
