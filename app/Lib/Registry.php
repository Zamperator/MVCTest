<?php

namespace App\Lib;

/**
 * @package App\Lib
 * @version 1.0
 * @since 1.0
 */
class Registry
{
    /**
     * @var array
     */
    private static array $dataStore = [];

    /**
     * @param string $index
     * @param mixed $data
     * @return void
     */
    public static function set(string $index, mixed $data): void
    {
        self::$dataStore[$index] = $data;
    }

    /**
     * @param string $index
     * @param string $subIndex
     * @return mixed
     */
    public static function get(string $index = '', string $subIndex = ''): mixed
    {
        return ($subIndex) ? self::$dataStore[$index][$subIndex] ?? false : self::$dataStore[$index] ?? false;
    }

    /**
     * @param string $index
     * @param string $subIndex
     * @return void
     */
    public static function delete(string $index = '', string $subIndex = ''): void
    {
        if ($subIndex) {
            unset(self::$dataStore[$index][$subIndex]);
        } else {
            unset(self::$dataStore[$index]);
        }
    }
}
