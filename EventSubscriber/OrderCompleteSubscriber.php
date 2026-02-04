<?php

namespace Plugin\ClaudeSample\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Customer;
use Eccube\Entity\Order;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Plugin\ClaudeSample\Repository\GroupRepository;
use Plugin\ClaudeSample\Service\Strategy\GroupStrategyContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderCompleteSubscriber implements EventSubscriberInterface
{
    private GroupStrategyContext $strategyContext;
    private GroupRepository $groupRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        GroupStrategyContext $strategyContext,
        GroupRepository $groupRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->strategyContext = $strategyContext;
        $this->groupRepository = $groupRepository;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EccubeEvents::FRONT_SHOPPING_COMPLETE_INITIALIZE => 'onShoppingComplete',
        ];
    }

    public function onShoppingComplete(EventArgs $event): void
    {
        /** @var Order $Order */
        $Order = $event->getArgument('Order');
        $Customer = $Order->getCustomer();

        if (!$Customer instanceof Customer) {
            return;
        }

        $this->updateCustomerGroups($Customer);
    }

    private function updateCustomerGroups(Customer $Customer): void
    {
        $allGroups = $this->groupRepository->findAll();
        $matchedGroups = $this->strategyContext->evaluateAll($Customer, $allGroups);

        foreach ($matchedGroups as $group) {
            if (!$Customer->getClaudeSampleGroups()->contains($group)) {
                $Customer->addClaudeSampleGroup($group);
                log_info('[ClaudeSample] グループを付与しました', [
                    'customer_id' => $Customer->getId(),
                    'group_name' => $group->getName(),
                ]);
            }
        }

        $this->entityManager->flush();
    }
}
