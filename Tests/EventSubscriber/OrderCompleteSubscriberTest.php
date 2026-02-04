<?php

namespace Plugin\ClaudeSample\Tests\EventSubscriber;

use Eccube\Entity\Order;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Tests\EccubeTestCase;
use Plugin\ClaudeSample\EventSubscriber\OrderCompleteSubscriber;
use Plugin\ClaudeSample\Repository\GroupRepository;
use Plugin\ClaudeSample\Service\Strategy\GroupStrategyContext;
use Plugin\ClaudeSample\Service\Strategy\PurchaseAmountStrategy;
use Plugin\ClaudeSample\Service\Strategy\PurchaseCountStrategy;
use Plugin\ClaudeSample\Tests\TestCaseTrait;
use Symfony\Component\HttpFoundation\Request;

class OrderCompleteSubscriberTest extends EccubeTestCase
{
    use TestCaseTrait;

    private ?OrderCompleteSubscriber $subscriber = null;
    private ?GroupRepository $groupRepository = null;
    private ?GroupStrategyContext $strategyContext = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->groupRepository = static::getContainer()->get(GroupRepository::class);

        $this->strategyContext = new GroupStrategyContext();
        $this->strategyContext->addStrategy(new PurchaseAmountStrategy());
        $this->strategyContext->addStrategy(new PurchaseCountStrategy());

        $this->subscriber = new OrderCompleteSubscriber(
            $this->strategyContext,
            $this->groupRepository,
            $this->entityManager
        );
    }

    public function testGetSubscribedEvents(): void
    {
        $events = OrderCompleteSubscriber::getSubscribedEvents();

        self::assertArrayHasKey(EccubeEvents::FRONT_SHOPPING_COMPLETE_INITIALIZE, $events);
        self::assertSame('onShoppingComplete', $events[EccubeEvents::FRONT_SHOPPING_COMPLETE_INITIALIZE]);
    }

    public function testOnShoppingCompleteAssignsGoldGroup(): void
    {
        // グループを作成
        $goldGroup = $this->createGroup('Gold');

        // 顧客を作成して購入金額を設定
        $customer = $this->createCustomer();
        $customer->setBuyTotal(100000);
        $this->entityManager->flush();

        // 注文を作成
        $order = new Order();
        $order->setCustomer($customer);

        // イベントを発火
        $request = Request::create('/');
        $event = new EventArgs(['Order' => $order], $request);
        $this->subscriber->onShoppingComplete($event);

        // グループが付与されていることを確認
        self::assertTrue($customer->getClaudeSampleGroups()->contains($goldGroup));
    }

    public function testOnShoppingCompleteAssignsSilverGroup(): void
    {
        $silverGroup = $this->createGroup('Silver');

        $customer = $this->createCustomer();
        $customer->setBuyTotal(50000);
        $this->entityManager->flush();

        $order = new Order();
        $order->setCustomer($customer);

        $request = Request::create('/');
        $event = new EventArgs(['Order' => $order], $request);
        $this->subscriber->onShoppingComplete($event);

        self::assertTrue($customer->getClaudeSampleGroups()->contains($silverGroup));
    }

    public function testOnShoppingCompleteAssignsBronzeGroup(): void
    {
        $bronzeGroup = $this->createGroup('Bronze');

        $customer = $this->createCustomer();
        $customer->setBuyTotal(10000);
        $this->entityManager->flush();

        $order = new Order();
        $order->setCustomer($customer);

        $request = Request::create('/');
        $event = new EventArgs(['Order' => $order], $request);
        $this->subscriber->onShoppingComplete($event);

        self::assertTrue($customer->getClaudeSampleGroups()->contains($bronzeGroup));
    }

    public function testOnShoppingCompleteDoesNotAssignGroupWhenNotMet(): void
    {
        $goldGroup = $this->createGroup('Gold');

        $customer = $this->createCustomer();
        $customer->setBuyTotal(0);
        $customer->setBuyTimes(0);
        $this->entityManager->flush();

        $order = new Order();
        $order->setCustomer($customer);

        $request = Request::create('/');
        $event = new EventArgs(['Order' => $order], $request);
        $this->subscriber->onShoppingComplete($event);

        self::assertFalse($customer->getClaudeSampleGroups()->contains($goldGroup));
    }

    public function testOnShoppingCompleteWithNonMemberOrder(): void
    {
        $goldGroup = $this->createGroup('Gold');

        // 非会員注文（Customerがnull）
        $order = new Order();

        $request = Request::create('/');
        $event = new EventArgs(['Order' => $order], $request);

        // エラーが発生しないことを確認
        $this->subscriber->onShoppingComplete($event);

        self::assertTrue(true);
    }

    public function testOnShoppingCompleteDoesNotDuplicateGroup(): void
    {
        $goldGroup = $this->createGroup('Gold');

        $customer = $this->createCustomer();
        $customer->setBuyTotal(100000);
        $customer->addClaudeSampleGroup($goldGroup);
        $this->entityManager->flush();

        $order = new Order();
        $order->setCustomer($customer);

        $request = Request::create('/');
        $event = new EventArgs(['Order' => $order], $request);
        $this->subscriber->onShoppingComplete($event);

        // グループが重複していないことを確認
        $groupCount = $customer->getClaudeSampleGroups()->filter(function ($g) use ($goldGroup) {
            return $g->getId() === $goldGroup->getId();
        })->count();

        self::assertSame(1, $groupCount);
    }

    public function testOnShoppingCompleteAssignsMultipleGroups(): void
    {
        $goldGroup = $this->createGroup('Gold');
        $silverGroup = $this->createGroup('Silver');
        $bronzeGroup = $this->createGroup('Bronze');

        $customer = $this->createCustomer();
        $customer->setBuyTotal(100000);
        $this->entityManager->flush();

        $order = new Order();
        $order->setCustomer($customer);

        $request = Request::create('/');
        $event = new EventArgs(['Order' => $order], $request);
        $this->subscriber->onShoppingComplete($event);

        // すべてのグループが付与されていることを確認
        self::assertTrue($customer->getClaudeSampleGroups()->contains($goldGroup));
        self::assertTrue($customer->getClaudeSampleGroups()->contains($silverGroup));
        self::assertTrue($customer->getClaudeSampleGroups()->contains($bronzeGroup));
    }
}
