<?php

namespace App\Lib\Cache;

use App\Lib\Utils;
use Exception;
use PDO;
use PDOException;

/**
 * @uses Factory
 * @uses Methods
 * @package App\Lib\Cache
 * @version 1.0
 * @since 1.0
 * @see https://www.php.net/manual/de/book.pdo.php
 */
class Database extends Factory implements Methods
{
    protected PDO $db;
    protected string $table = 'cache';

    public function __construct()
    {
        parent::__construct();

        try {
            $this->db = Database::get($this->config['db']['connection']['use']);
        } catch (Exception $e) {
        }
    }

    /**
     * @param string $cacheKey
     * @return bool
     */
    public function delete(string $cacheKey): bool
    {
        $cacheKey = $this->cleanUpKey($cacheKey);

        if ($cacheKey) {

            $sthDeleteCache = $this->db->prepare(
                'DELETE FROM ' . $this->table . ' WHERE cache_key = :cache_key'
            );
            $sthDeleteCache->bindValue(':cache_key', $cacheKey);

            return $sthDeleteCache->execute();
        }

        return false;
    }

    /**
     * @param string $cacheKey
     * @return mixed|string
     */
    public function get(string $cacheKey): mixed
    {
        $sResult = '';

        $cacheKey = $this->cleanUpKey($cacheKey);

        if ($this->clear($cacheKey, $this)) {
            return $sResult;
        }

        $sSQLSelectCache = 'SELECT CONVERT(value USING UTF8) as value, expires FROM ' . $this->table . ' WHERE cache_key = :cache_key';

        $sthGetCache = $this->db->prepare($sSQLSelectCache);
        $sthGetCache->bindValue(':cache_key', $cacheKey);

        if ($sthGetCache->execute()) {
            $rawFetch = $sthGetCache->fetch(PDO::FETCH_ASSOC);
            if (!empty($rawFetch)) {
                if ((int)$rawFetch['expires'] && $rawFetch['expires'] < time()) {
                    $this->delete($cacheKey);
                } else {
                    $sResult = $rawFetch['value'];
                }
            }
            unset($aRawFetch);
        }

        return $sResult;
    }

    /**
     * @param string $cacheKey
     * @param mixed $insertValue
     * @param int $expiresIn
     * @return void
     */
    public function set(string $cacheKey, mixed $insertValue, int $expiresIn = 0): void
    {
        $cacheKey = $this->cleanUpKey($cacheKey);

        if (is_object($insertValue)) {
            die('Objects are not allowed to be stored in memcache');
        }

        if (is_array($insertValue)) {
            $insertValue = $this->cleanUpRecursive($insertValue);
        } else {
            $insertValue = Utils::cleanup($insertValue);
        }

        if ($expiresIn > 0) {
            $expiresIn = TIME_NOW + $expiresIn;
        }

        $sSQLInsertCache = 'REPLACE INTO ' . $this->table . ' (cache_key, value, expires) VALUES (:cache_key, CAST(:value AS BINARY), :expires)';

        $sthInsertCache = $this->db->prepare($sSQLInsertCache);
        $sthInsertCache->bindValue(':cache_key', $cacheKey);
        $sthInsertCache->bindValue(':value', $insertValue);
        $sthInsertCache->bindValue(':expires', $expiresIn, PDO::PARAM_INT);

        try {
            $sthInsertCache->execute();
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}
