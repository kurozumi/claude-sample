<?php

namespace Plugin\ClaudeSample\Tests;

use Plugin\ClaudeSample\Entity\Group;

trait TestCaseTrait
{
    protected function createGroup(string $name = 'テストグループ', int $sortNo = null): Group
    {
        if ($sortNo === null) {
            $lastGroup = $this->entityManager->getRepository(Group::class)->findOneBy([], ['sortNo' => 'DESC']);
            $sortNo = $lastGroup ? $lastGroup->getSortNo() + 1 : 1;
        }

        $group = new Group();
        $group->setName($name);
        $group->setSortNo($sortNo);
        $this->entityManager->persist($group);
        $this->entityManager->flush();

        return $group;
    }
}
