<?php

namespace Plugin\ClaudeSample\Tests\Service\Strategy;

use Eccube\Tests\EccubeTestCase;
use Plugin\ClaudeSample\Service\Strategy\PurchaseCountStrategy;
use Plugin\ClaudeSample\Tests\TestCaseTrait;

class PurchaseCountStrategyTest extends EccubeTestCase
{
    use TestCaseTrait;

    private ?PurchaseCountStrategy $strategy = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new PurchaseCountStrategy();
    }

    public function testSupportsWithGoldGroup(): void
    {
        $group = $this->createGroup('Gold');
        self::assertTrue($this->strategy->supports($group));
    }

    public function testSupportsWithSilverGroup(): void
    {
        $group = $this->createGroup('Silver');
        self::assertTrue($this->strategy->supports($group));
    }

    public function testSupportsWithBronzeGroup(): void
    {
        $group = $this->createGroup('Bronze');
        self::assertTrue($this->strategy->supports($group));
    }

    public function testSupportsWithUnknownGroup(): void
    {
        $group = $this->createGroup('Unknown');
        self::assertFalse($this->strategy->supports($group));
    }

    public function testMatchesGoldWithSufficientCount(): void
    {
        $group = $this->createGroup('Gold');
        $customer = $this->createCustomer();
        $customer->setBuyTimes(10);

        self::assertTrue($this->strategy->matches($customer, $group));
    }

    public function testMatchesGoldWithInsufficientCount(): void
    {
        $group = $this->createGroup('Gold');
        $customer = $this->createCustomer();
        $customer->setBuyTimes(9);

        self::assertFalse($this->strategy->matches($customer, $group));
    }

    public function testMatchesSilverWithSufficientCount(): void
    {
        $group = $this->createGroup('Silver');
        $customer = $this->createCustomer();
        $customer->setBuyTimes(5);

        self::assertTrue($this->strategy->matches($customer, $group));
    }

    public function testMatchesSilverWithInsufficientCount(): void
    {
        $group = $this->createGroup('Silver');
        $customer = $this->createCustomer();
        $customer->setBuyTimes(4);

        self::assertFalse($this->strategy->matches($customer, $group));
    }

    public function testMatchesBronzeWithSufficientCount(): void
    {
        $group = $this->createGroup('Bronze');
        $customer = $this->createCustomer();
        $customer->setBuyTimes(2);

        self::assertTrue($this->strategy->matches($customer, $group));
    }

    public function testMatchesBronzeWithInsufficientCount(): void
    {
        $group = $this->createGroup('Bronze');
        $customer = $this->createCustomer();
        $customer->setBuyTimes(1);

        self::assertFalse($this->strategy->matches($customer, $group));
    }

    public function testGetPriority(): void
    {
        self::assertSame(90, PurchaseCountStrategy::getPriority());
    }

    public function testGetName(): void
    {
        self::assertSame('購入回数判定', $this->strategy->getName());
    }
}
