<?php

namespace App\Lib\Cache;

interface Methods
{
    public function delete(string $cacheKey): bool;

    public function get(string $cacheKey): mixed;

    public function set(string $cacheKey, mixed $insertValue, int $expiresIn = 0): void;
}
