<?php

namespace Plugin\ClaudeSample\Tests\Service\Strategy;

use Eccube\Tests\EccubeTestCase;
use Plugin\ClaudeSample\Service\Strategy\GroupStrategyContext;
use Plugin\ClaudeSample\Service\Strategy\PurchaseAmountStrategy;
use Plugin\ClaudeSample\Service\Strategy\PurchaseCountStrategy;
use Plugin\ClaudeSample\Tests\TestCaseTrait;

class GroupStrategyContextTest extends EccubeTestCase
{
    use TestCaseTrait;

    private ?GroupStrategyContext $context = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->context = new GroupStrategyContext();
        $this->context->addStrategy(new PurchaseAmountStrategy());
        $this->context->addStrategy(new PurchaseCountStrategy());
    }

    public function testGetStrategies(): void
    {
        self::assertCount(2, $this->context->getStrategies());
    }

    public function testEvaluateWithMatchingAmountStrategy(): void
    {
        $group = $this->createGroup('Gold');
        $customer = $this->createCustomer();
        $customer->setBuyTotal(100000);
        $customer->setBuyTimes(0);

        self::assertTrue($this->context->evaluate($customer, $group));
    }

    public function testEvaluateWithMatchingCountStrategy(): void
    {
        $group = $this->createGroup('Gold');
        $customer = $this->createCustomer();
        $customer->setBuyTotal(0);
        $customer->setBuyTimes(10);

        self::assertTrue($this->context->evaluate($customer, $group));
    }

    public function testEvaluateWithNoMatchingStrategy(): void
    {
        $group = $this->createGroup('Gold');
        $customer = $this->createCustomer();
        $customer->setBuyTotal(0);
        $customer->setBuyTimes(0);

        self::assertFalse($this->context->evaluate($customer, $group));
    }

    public function testEvaluateWithUnsupportedGroup(): void
    {
        $group = $this->createGroup('Platinum');
        $customer = $this->createCustomer();
        $customer->setBuyTotal(1000000);
        $customer->setBuyTimes(100);

        self::assertFalse($this->context->evaluate($customer, $group));
    }

    public function testEvaluateAllWithMultipleMatchingGroups(): void
    {
        $goldGroup = $this->createGroup('Gold');
        $silverGroup = $this->createGroup('Silver');
        $bronzeGroup = $this->createGroup('Bronze');

        $customer = $this->createCustomer();
        $customer->setBuyTotal(100000);

        $matchedGroups = $this->context->evaluateAll($customer, [$goldGroup, $silverGroup, $bronzeGroup]);

        self::assertCount(3, $matchedGroups);
        self::assertContains($goldGroup, $matchedGroups);
        self::assertContains($silverGroup, $matchedGroups);
        self::assertContains($bronzeGroup, $matchedGroups);
    }

    public function testEvaluateAllWithPartialMatchingGroups(): void
    {
        $goldGroup = $this->createGroup('Gold');
        $silverGroup = $this->createGroup('Silver');
        $bronzeGroup = $this->createGroup('Bronze');

        $customer = $this->createCustomer();
        $customer->setBuyTotal(50000);

        $matchedGroups = $this->context->evaluateAll($customer, [$goldGroup, $silverGroup, $bronzeGroup]);

        self::assertCount(2, $matchedGroups);
        self::assertNotContains($goldGroup, $matchedGroups);
        self::assertContains($silverGroup, $matchedGroups);
        self::assertContains($bronzeGroup, $matchedGroups);
    }

    public function testEvaluateAllWithNoMatchingGroups(): void
    {
        $goldGroup = $this->createGroup('Gold');
        $silverGroup = $this->createGroup('Silver');
        $bronzeGroup = $this->createGroup('Bronze');

        $customer = $this->createCustomer();
        $customer->setBuyTotal(0);
        $customer->setBuyTimes(0);

        $matchedGroups = $this->context->evaluateAll($customer, [$goldGroup, $silverGroup, $bronzeGroup]);

        self::assertCount(0, $matchedGroups);
    }
}
