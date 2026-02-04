<?php

namespace Plugin\ClaudeSample\Tests\Repository;

use Eccube\Tests\EccubeTestCase;
use Plugin\ClaudeSample\Entity\Group;
use Plugin\ClaudeSample\Repository\GroupRepository;
use Plugin\ClaudeSample\Tests\TestCaseTrait;

class GroupRepositoryTest extends EccubeTestCase
{
    use TestCaseTrait;

    private ?GroupRepository $groupRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->groupRepository = static::getContainer()->get(GroupRepository::class);
    }

    public function testGetQueryBuilderBySearchData(): void
    {
        $group1 = $this->createGroup('グループ1', 1);
        $group2 = $this->createGroup('グループ2', 2);

        $results = $this->groupRepository->getQueryBuilderBySearchData()
            ->getQuery()
            ->getResult();

        self::assertGreaterThanOrEqual(2, count($results));
    }

    public function testGetQueryBuilderBySearchDataOrderBySortNo(): void
    {
        // 既存のデータをクリア
        foreach ($this->groupRepository->findAll() as $group) {
            $this->entityManager->remove($group);
        }
        $this->entityManager->flush();

        $group2 = $this->createGroup('グループ2', 2);
        $group1 = $this->createGroup('グループ1', 1);
        $group3 = $this->createGroup('グループ3', 3);

        $results = $this->groupRepository->getQueryBuilderBySearchData()
            ->getQuery()
            ->getResult();

        self::assertCount(3, $results);
        self::assertSame('グループ1', $results[0]->getName());
        self::assertSame('グループ2', $results[1]->getName());
        self::assertSame('グループ3', $results[2]->getName());
    }

    public function testGetMaxSortNo(): void
    {
        // 既存のデータをクリア
        foreach ($this->groupRepository->findAll() as $group) {
            $this->entityManager->remove($group);
        }
        $this->entityManager->flush();

        self::assertSame(0, $this->groupRepository->getMaxSortNo());

        $this->createGroup('グループ1', 5);
        self::assertSame(5, $this->groupRepository->getMaxSortNo());

        $this->createGroup('グループ2', 10);
        self::assertSame(10, $this->groupRepository->getMaxSortNo());
    }

    public function testFindAll(): void
    {
        $initialCount = count($this->groupRepository->findAll());

        $this->createGroup('新規グループ');

        self::assertCount($initialCount + 1, $this->groupRepository->findAll());
    }
}
