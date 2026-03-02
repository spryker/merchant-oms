<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantOms\Business\EventTrigger;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer;
use Generated\Shared\Transfer\MerchantOmsTriggerResponseTransfer;
use Generated\Shared\Transfer\MerchantOrderItemCriteriaTransfer;
use Generated\Shared\Transfer\MerchantOrderItemTransfer;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Spryker\Zed\MerchantOms\Business\StateMachineProcess\StateMachineProcessReaderInterface;
use Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToMerchantSalesOrderFacadeInterface;
use Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToStateMachineFacadeInterface;

class MerchantOmsEventTrigger implements MerchantOmsEventTriggerInterface
{
    /**
     * @var \Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToStateMachineFacadeInterface
     */
    protected $stateMachineFacade;

    /**
     * @var \Spryker\Zed\MerchantOms\Business\StateMachineProcess\StateMachineProcessReaderInterface
     */
    protected $stateMachineProcessReader;

    /**
     * @var \Spryker\Zed\MerchantOms\Dependency\Facade\MerchantOmsToMerchantSalesOrderFacadeInterface
     */
    protected $merchantSalesOrderFacade;

    public function __construct(
        MerchantOmsToStateMachineFacadeInterface $stateMachineFacade,
        StateMachineProcessReaderInterface $stateMachineProcessReader,
        MerchantOmsToMerchantSalesOrderFacadeInterface $merchantSalesOrderFacade
    ) {
        $this->stateMachineFacade = $stateMachineFacade;
        $this->stateMachineProcessReader = $stateMachineProcessReader;
        $this->merchantSalesOrderFacade = $merchantSalesOrderFacade;
    }

    public function triggerForNewMerchantOrderItems(MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer): int
    {
        $merchantOmsTriggerRequestTransfer
            ->requireMerchantReference()
            ->requireMerchantOrderItems();

        $stateMachineProcessTransfer = $this->stateMachineProcessReader
            ->resolveMerchantStateMachineProcess(
                (new MerchantCriteriaTransfer())->setMerchantReference($merchantOmsTriggerRequestTransfer->getMerchantReference()),
            );

        $transitionCount = 0;
        foreach ($merchantOmsTriggerRequestTransfer->getMerchantOrderItems() as $merchantOrderItemTransfer) {
            /** @var int $idMerchantOrderItem */
            $idMerchantOrderItem = $merchantOrderItemTransfer->getIdMerchantOrderItem();

            $transitionCount += $this->stateMachineFacade->triggerForNewStateMachineItem(
                $stateMachineProcessTransfer,
                $idMerchantOrderItem,
            );
        }

        return $transitionCount;
    }

    public function triggerEventForMerchantOrderItems(MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer): int
    {
        $merchantOmsTriggerRequestTransfer
            ->requireMerchantOrderItems()
            ->requireMerchantOmsEventName();

        $stateMachineItemTransfers = [];

        foreach ($merchantOmsTriggerRequestTransfer->getMerchantOrderItems() as $merchantOrderItemTransfer) {
            $stateMachineItemTransfers[] = $this->createStateMachineItem($merchantOrderItemTransfer);
        }
        /** @var string $merchantOmsEventName */
        $merchantOmsEventName = $merchantOmsTriggerRequestTransfer->getMerchantOmsEventName();

        $transitionCount = $this->stateMachineFacade->triggerEventForItems(
            $merchantOmsEventName,
            $stateMachineItemTransfers,
        );

        return $transitionCount;
    }

    public function triggerEventForMerchantOrderItem(
        MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer
    ): MerchantOmsTriggerResponseTransfer {
        $merchantOmsTriggerRequestTransfer->requireMerchantOrderItemReference();
        $merchantOmsTriggerRequestTransfer->requireMerchantOmsEventName();

        $merchantOrderItemCriteriaTransfer = (new MerchantOrderItemCriteriaTransfer())
            ->setMerchantOrderItemReference($merchantOmsTriggerRequestTransfer->getMerchantOrderItemReference());

        $merchantOmsTriggerResponseTransfer = new MerchantOmsTriggerResponseTransfer();

        $merchantOrderItemTransfer = $this->merchantSalesOrderFacade->findMerchantOrderItem($merchantOrderItemCriteriaTransfer);

        if (!$merchantOrderItemTransfer) {
            /** @var string $merchantOrderItemReference */
            $merchantOrderItemReference = $merchantOmsTriggerRequestTransfer->getMerchantOrderItemReference();

            return $merchantOmsTriggerResponseTransfer->setIsSuccessful(false)
                ->setMessage(sprintf(
                    'Merchant order item with reference "%s" was not found.',
                    $merchantOrderItemReference,
                ));
        }

        $transitionedItemsCount = $this->triggerEventForMerchantOrderItems(
            (new MerchantOmsTriggerRequestTransfer())
                ->setMerchantOmsEventName($merchantOmsTriggerRequestTransfer->getMerchantOmsEventName())
                ->addMerchantOrderItem($merchantOrderItemTransfer),
        );

        if (!$transitionedItemsCount) {
            /** @var string $merchantOmsEventName */
            $merchantOmsEventName = $merchantOmsTriggerRequestTransfer->getMerchantOmsEventName();

            /** @var string $merchantOrderItemReference */
            $merchantOrderItemReference = $merchantOmsTriggerRequestTransfer->getMerchantOrderItemReference();

            return $merchantOmsTriggerResponseTransfer->setIsSuccessful(false)
                ->setMessage(sprintf(
                    'Event "%s" was not successfully triggered for merchant order item with reference "%s".',
                    $merchantOmsEventName,
                    $merchantOrderItemReference,
                ));
        }

        return $merchantOmsTriggerResponseTransfer->setIsSuccessful(true)->setMessage('Success.');
    }

    protected function createStateMachineItem(MerchantOrderItemTransfer $merchantOrderItemTransfer): StateMachineItemTransfer
    {
        return (new StateMachineItemTransfer())
            ->setIdItemState($merchantOrderItemTransfer->getFkStateMachineItemState())
            ->setIdentifier($merchantOrderItemTransfer->getIdMerchantOrderItem());
    }
}
