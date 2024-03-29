<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantOms\Business;

use Generated\Shared\Transfer\MerchantCriteriaTransfer;
use Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer;
use Generated\Shared\Transfer\MerchantOmsTriggerResponseTransfer;
use Generated\Shared\Transfer\MerchantOrderItemCollectionTransfer;
use Generated\Shared\Transfer\MerchantOrderTransfer;
use Generated\Shared\Transfer\StateMachineItemTransfer;
use Generated\Shared\Transfer\StateMachineProcessTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\MerchantOms\Business\MerchantOmsBusinessFactory getFactory()
 * @method \Spryker\Zed\MerchantOms\Persistence\MerchantOmsRepositoryInterface getRepository()
 */
class MerchantOmsFacade extends AbstractFacade implements MerchantOmsFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer
     *
     * @return int
     */
    public function triggerForNewMerchantOrderItems(MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer): int
    {
        return $this->getFactory()->createMerchantOmsEventTrigger()->triggerForNewMerchantOrderItems($merchantOmsTriggerRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer
     *
     * @return int
     */
    public function triggerEventForMerchantOrderItems(MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer): int
    {
        return $this->getFactory()->createMerchantOmsEventTrigger()->triggerEventForMerchantOrderItems($merchantOmsTriggerRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<int> $stateIds
     *
     * @return array<\Generated\Shared\Transfer\StateMachineItemTransfer>
     */
    public function getStateMachineItemsByStateIds(array $stateIds): array
    {
        return $this->getRepository()->getStateMachineItemsByStateIds($stateIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantOmsTriggerResponseTransfer
     */
    public function triggerEventForMerchantOrderItem(
        MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer
    ): MerchantOmsTriggerResponseTransfer {
        return $this->getFactory()->createMerchantOmsEventTrigger()->triggerEventForMerchantOrderItem($merchantOmsTriggerRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantCriteriaTransfer $merchantCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\StateMachineProcessTransfer
     */
    public function getMerchantOmsProcessByMerchant(
        MerchantCriteriaTransfer $merchantCriteriaTransfer
    ): StateMachineProcessTransfer {
        return $this->getFactory()->createStateMachineProcessReader()->getMerchantOmsProcessByMerchant($merchantCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantOrderTransfer $merchantOrderTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantOrderTransfer
     */
    public function expandMerchantOrderWithMerchantOmsData(MerchantOrderTransfer $merchantOrderTransfer): MerchantOrderTransfer
    {
        return $this->getFactory()->createMerchantOrderExpander()->expandMerchantOrderWithMerchantOmsData($merchantOrderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idSalesOrderItem
     *
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer|null
     */
    public function findCurrentStateByIdSalesOrderItem(int $idSalesOrderItem): ?StateMachineItemTransfer
    {
        return $this->getRepository()->findCurrentStateByIdSalesOrderItem($idSalesOrderItem);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param array<int> $merchantOrderItemIds
     *
     * @return array<int, array<\Generated\Shared\Transfer\StateMachineItemTransfer>>
     */
    public function getMerchantOrderItemsStateHistory(array $merchantOrderItemIds): array
    {
        return $this->getFactory()
            ->createMerchantOmsReader()
            ->getMerchantOrderItemsStateHistory($merchantOrderItemIds);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantOrderItemCollectionTransfer $merchantOrderItemCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\MerchantOrderItemCollectionTransfer
     */
    public function expandMerchantOrderItemsWithManualEvents(
        MerchantOrderItemCollectionTransfer $merchantOrderItemCollectionTransfer
    ): MerchantOrderItemCollectionTransfer {
        return $this->getFactory()->createMerchantOrderItemsExpander()->expandMerchantOrderItemsWithManualEvents(
            $merchantOrderItemCollectionTransfer,
        );
    }
}
