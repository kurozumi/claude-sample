<?php

namespace Plugin\ClaudeSample\Service\Strategy;

use Eccube\Entity\Customer;
use Plugin\ClaudeSample\Entity\Group;

interface GroupStrategyInterface
{
    /**
     * このストラテジーが指定されたグループをサポートするかを判定する
     */
    public function supports(Group $group): bool;

    /**
     * 顧客がグループに所属すべきかを判定する
     */
    public function matches(Customer $customer, Group $group): bool;

    /**
     * ストラテジーの優先度を返す（大きいほど先に評価される）
     */
    public static function getPriority(): int;

    /**
     * ストラテジーの名前を返す
     */
    public function getName(): string;
}
