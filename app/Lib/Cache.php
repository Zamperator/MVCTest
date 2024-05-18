<?php

namespace App\Lib;

use Exception;

/**
 * Class Cache
 */
class Cache
{
    protected mixed $cacheObject;
    protected array $config;

    /**
     * Cache object is usable everywhere. Just initialize it from Registry::get('cache_object')
     */
    public function __construct()
    {
        $this->config = Registry::get('config');

        $cacheSettings = $this->config['cache']['classes'][$this->config['cache']['active']] ?? false;

        if (empty($cacheSettings)) {
            return;
        }

        $this->cacheObject = Registry::get('cache_object');

        if (!($this->cacheObject instanceof $cacheSettings['method'])) {

            if (!class_exists($cacheSettings['method'])) {
                die('Cache-Methode ' . $cacheSettings['method'] . ' existiert nicht.');
            }

            try {
                $this->cacheObject = new $cacheSettings['method']($this->config);

                if ($cacheSettings['method'] == 'Memcached' && method_exists($cacheSettings['method'], 'addServer')) {
                    foreach ($cacheSettings['server'] as $server) {
                        $this->cacheObject->addServer($server['host'], $server['port']);
                    }
                }

                Registry::set('cache_object', $this->cacheObject);

            } catch (Exception $e) {
                die($e->getMessage());
            }
        }
    }

    /**
     * @return false|mixed
     */
    public function get(): mixed
    {
        return (!$this->cacheObject) ? false : $this->cacheObject;
    }
}
