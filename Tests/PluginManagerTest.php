<?php

namespace Plugin\ClaudeSample\Tests;

use Eccube\Tests\EccubeTestCase;
use Plugin\ClaudeSample\PluginManager;

class PluginManagerTest extends EccubeTestCase
{
    private ?PluginManager $pluginManager = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pluginManager = new PluginManager();
    }

    public function testEnable(): void
    {
        $meta = [];
        $this->pluginManager->enable($meta, static::getContainer());

        self::assertTrue(true);
    }

    public function testDisable(): void
    {
        $meta = [];
        $this->pluginManager->disable($meta, static::getContainer());

        self::assertTrue(true);
    }

    public function testUpdate(): void
    {
        $meta = [];
        $this->pluginManager->update($meta, static::getContainer());

        self::assertTrue(true);
    }

    public function testUninstall(): void
    {
        $meta = [];
        $this->pluginManager->uninstall($meta, static::getContainer());

        self::assertTrue(true);
    }
}
