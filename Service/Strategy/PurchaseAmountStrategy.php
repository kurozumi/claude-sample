<?php

namespace Plugin\ClaudeSample\Service\Strategy;

use Eccube\Entity\Customer;
use Plugin\ClaudeSample\Entity\Group;

/**
 * 購入金額に基づいてグループを判定するストラテジー
 *
 * グループ名に基づいて購入金額の閾値を設定:
 * - Gold: 100,000円以上
 * - Silver: 50,000円以上
 * - Bronze: 10,000円以上
 */
class PurchaseAmountStrategy implements GroupStrategyInterface
{
    private const THRESHOLDS = [
        'Gold' => 100000,
        'Silver' => 50000,
        'Bronze' => 10000,
    ];

    public function supports(Group $group): bool
    {
        return array_key_exists($group->getName(), self::THRESHOLDS);
    }

    public function matches(Customer $customer, Group $group): bool
    {
        $buyTotal = (float) $customer->getBuyTotal();
        $threshold = self::THRESHOLDS[$group->getName()] ?? 0;

        return $buyTotal >= $threshold;
    }

    public static function getPriority(): int
    {
        return 100;
    }

    public function getName(): string
    {
        return '購入金額判定';
    }
}
