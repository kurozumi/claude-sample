<?php

namespace Plugin\ClaudeSample\Service\Strategy;

use Eccube\Entity\Customer;
use Plugin\ClaudeSample\Entity\Group;

class GroupStrategyContext
{
    /**
     * @var GroupStrategyInterface[]
     */
    private array $strategies = [];

    public function addStrategy(GroupStrategyInterface $strategy): void
    {
        $this->strategies[] = $strategy;
    }

    /**
     * @return GroupStrategyInterface[]
     */
    public function getStrategies(): array
    {
        return $this->strategies;
    }

    /**
     * 顧客がグループに所属すべきかを全ストラテジーで評価する
     */
    public function evaluate(Customer $customer, Group $group): bool
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($group) && $strategy->matches($customer, $group)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 顧客に適用されるべき全グループを返す
     *
     * @param Group[] $allGroups
     * @return Group[]
     */
    public function evaluateAll(Customer $customer, array $allGroups): array
    {
        $matchedGroups = [];

        foreach ($allGroups as $group) {
            if ($this->evaluate($customer, $group)) {
                $matchedGroups[] = $group;
            }
        }

        return $matchedGroups;
    }
}
