<?php

namespace Plugin\ClaudeSample\Tests\Service\Strategy;

use Eccube\Tests\EccubeTestCase;
use Plugin\ClaudeSample\Service\Strategy\PurchaseAmountStrategy;
use Plugin\ClaudeSample\Tests\TestCaseTrait;

class PurchaseAmountStrategyTest extends EccubeTestCase
{
    use TestCaseTrait;

    private ?PurchaseAmountStrategy $strategy = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->strategy = new PurchaseAmountStrategy();
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

    public function testMatchesGoldWithSufficientAmount(): void
    {
        $group = $this->createGroup('Gold');
        $customer = $this->createCustomer();
        $customer->setBuyTotal(100000);

        self::assertTrue($this->strategy->matches($customer, $group));
    }

    public function testMatchesGoldWithInsufficientAmount(): void
    {
        $group = $this->createGroup('Gold');
        $customer = $this->createCustomer();
        $customer->setBuyTotal(99999);

        self::assertFalse($this->strategy->matches($customer, $group));
    }

    public function testMatchesSilverWithSufficientAmount(): void
    {
        $group = $this->createGroup('Silver');
        $customer = $this->createCustomer();
        $customer->setBuyTotal(50000);

        self::assertTrue($this->strategy->matches($customer, $group));
    }

    public function testMatchesSilverWithInsufficientAmount(): void
    {
        $group = $this->createGroup('Silver');
        $customer = $this->createCustomer();
        $customer->setBuyTotal(49999);

        self::assertFalse($this->strategy->matches($customer, $group));
    }

    public function testMatchesBronzeWithSufficientAmount(): void
    {
        $group = $this->createGroup('Bronze');
        $customer = $this->createCustomer();
        $customer->setBuyTotal(10000);

        self::assertTrue($this->strategy->matches($customer, $group));
    }

    public function testMatchesBronzeWithInsufficientAmount(): void
    {
        $group = $this->createGroup('Bronze');
        $customer = $this->createCustomer();
        $customer->setBuyTotal(9999);

        self::assertFalse($this->strategy->matches($customer, $group));
    }

    public function testGetPriority(): void
    {
        self::assertSame(100, PurchaseAmountStrategy::getPriority());
    }

    public function testGetName(): void
    {
        self::assertSame('購入金額判定', $this->strategy->getName());
    }
}
