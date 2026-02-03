<?php

namespace Plugin\ClaudeSample;

use Eccube\Plugin\AbstractPluginManager;
use Psr\Container\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    public function enable(array $meta, ContainerInterface $container): void
    {
        // プラグイン有効化時の処理
    }

    public function disable(array $meta, ContainerInterface $container): void
    {
        // プラグイン無効化時の処理
    }

    public function update(array $meta, ContainerInterface $container): void
    {
        // プラグインアップデート時の処理
    }

    public function uninstall(array $meta, ContainerInterface $container): void
    {
        // プラグインアンインストール時の処理
    }
}
