<?php

namespace Plugin\ClaudeSample;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Plugin\AbstractPluginManager;
use Plugin\ClaudeSample\Entity\Group;
use Psr\Container\ContainerInterface;

class PluginManager extends AbstractPluginManager
{
    private const DEFAULT_GROUPS = [
        ['name' => 'Gold', 'sortNo' => 1],
        ['name' => 'Silver', 'sortNo' => 2],
        ['name' => 'Bronze', 'sortNo' => 3],
    ];

    public function enable(array $meta, ContainerInterface $container): void
    {
        $this->createDefaultGroups($container);
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

    private function createDefaultGroups(ContainerInterface $container): void
    {
        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine.orm.entity_manager');
        $groupRepository = $entityManager->getRepository(Group::class);

        foreach (self::DEFAULT_GROUPS as $groupData) {
            $existingGroup = $groupRepository->findOneBy(['name' => $groupData['name']]);
            if (null === $existingGroup) {
                $group = new Group();
                $group->setName($groupData['name']);
                $group->setSortNo($groupData['sortNo']);
                $entityManager->persist($group);
            }
        }

        $entityManager->flush();
    }
}
