<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantOms\Communication\Plugin\Sales;

use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\SalesExtension\Dependency\Plugin\OrderItemExpanderPluginInterface;

/**
 * @method \Spryker\Zed\MerchantOms\Business\MerchantOmsBusinessFactory getBusinessFactory()
 * @method \Spryker\Zed\MerchantOms\MerchantOmsConfig getConfig()
 */
class MerchantStateOrderItemExpanderPlugin extends AbstractPlugin implements OrderItemExpanderPluginInterface
{
    /**
     * {@inheritDoc}
     * - Reads current merchant order item states for the given order items in a single query.
     * - Sets `ItemTransfer.merchantStateMachineItem` for every item that has `ItemTransfer.idSalesOrderItem` set.
     * - Sets an empty `StateMachineItemTransfer` for items that do not belong to a merchant order item.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $itemTransfers
     *
     * @return array<\Generated\Shared\Transfer\ItemTransfer>
     */
    public function expand(array $itemTransfers): array
    {
        return $this->getBusinessFactory()
            ->createOrderItemMerchantStateExpander()
            ->expandOrderItemsWithMerchantState(array_values($itemTransfers));
    }
}
