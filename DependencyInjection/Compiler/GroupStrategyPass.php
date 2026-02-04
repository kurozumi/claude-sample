<?php

namespace Plugin\ClaudeSample\DependencyInjection\Compiler;

use Plugin\ClaudeSample\Service\Strategy\GroupStrategyContext;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class GroupStrategyPass implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public const TAG = 'claude_sample.group_strategy';

    public function process(ContainerBuilder $container): void
    {
        if (!$container->has(GroupStrategyContext::class)) {
            return;
        }

        $context = $container->findDefinition(GroupStrategyContext::class);

        foreach ($this->findAndSortTaggedServices(self::TAG, $container) as $id) {
            $context->addMethodCall('addStrategy', [new Reference($id)]);
        }
    }
}
