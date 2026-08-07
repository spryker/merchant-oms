<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantOms\Business\Expander;

use Generated\Shared\Transfer\StateMachineItemTransfer;
use Spryker\Zed\MerchantOms\Persistence\MerchantOmsRepositoryInterface;

class OrderItemMerchantStateExpander implements OrderItemMerchantStateExpanderInterface
{
    public function __construct(protected MerchantOmsRepositoryInterface $merchantOmsRepository)
    {
    }

    /**
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return array<\Generated\Shared\Transfer\ItemTransfer>
     */
    public function expandOrderItemsWithMerchantState(array $itemTransfers): array
    {
        $salesOrderItemIds = [];
        foreach ($itemTransfers as $itemTransfer) {
            if ($itemTransfer->getIdSalesOrderItem() === null) {
                continue;
            }

            $salesOrderItemIds[] = $itemTransfer->getIdSalesOrderItem();
        }

        $stateMachineItemTransfers = $this->merchantOmsRepository->getCurrentStatesIndexedByIdSalesOrderItem($salesOrderItemIds);

        foreach ($itemTransfers as $itemTransfer) {
            if ($itemTransfer->getIdSalesOrderItem() === null) {
                continue;
            }

            $itemTransfer->setMerchantStateMachineItem(
                $stateMachineItemTransfers[$itemTransfer->getIdSalesOrderItem()] ?? new StateMachineItemTransfer(),
            );
        }

        return $itemTransfers;
    }
}
