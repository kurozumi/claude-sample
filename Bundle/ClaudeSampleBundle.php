<?php

namespace Plugin\ClaudeSample\Bundle;

use Plugin\ClaudeSample\DependencyInjection\Compiler\GroupStrategyPass;
use Plugin\ClaudeSample\Service\Strategy\GroupStrategyInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ClaudeSampleBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->registerForAutoconfiguration(GroupStrategyInterface::class)
            ->addTag(GroupStrategyPass::TAG);

        $container->addCompilerPass(new GroupStrategyPass());
    }
}
