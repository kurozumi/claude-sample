<?php

namespace Plugin\ClaudeSample\Service\Strategy;

use Eccube\Entity\Customer;
use Plugin\ClaudeSample\Entity\Group;

/**
 * 購入回数に基づいてグループを判定するストラテジー
 *
 * グループ名に基づいて購入回数の閾値を設定:
 * - Gold: 10回以上
 * - Silver: 5回以上
 * - Bronze: 2回以上
 */
class PurchaseCountStrategy implements GroupStrategyInterface
{
    private const THRESHOLDS = [
        'Gold' => 10,
        'Silver' => 5,
        'Bronze' => 2,
    ];

    public function supports(Group $group): bool
    {
        return array_key_exists($group->getName(), self::THRESHOLDS);
    }

    public function matches(Customer $customer, Group $group): bool
    {
        $buyTimes = (int) $customer->getBuyTimes();
        $threshold = self::THRESHOLDS[$group->getName()] ?? 0;

        return $buyTimes >= $threshold;
    }

    public static function getPriority(): int
    {
        return 90;
    }

    public function getName(): string
    {
        return '購入回数判定';
    }
}
