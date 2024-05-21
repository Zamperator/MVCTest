<?php
// Example ...

namespace App;

use PHPUnit\Framework\TestCase;

final class GlobalTest extends TestCase
{
    public function testClassExists(): void
    {
        $this->assertTrue(class_exists("App\lib\Utils"));
        $this->assertTrue(class_exists("App\lib\Cache"));
        $this->assertTrue(class_exists("App\lib\Database"));
        $this->assertTrue(class_exists("App\lib\PageSetup"));
        $this->assertTrue(class_exists("App\lib\Registry"));
        $this->assertTrue(class_exists("App\Components\Controller"));
        $this->assertTrue(class_exists("App\Components\Model"));
        $this->assertTrue(class_exists("App\Components\View"));
    }
}