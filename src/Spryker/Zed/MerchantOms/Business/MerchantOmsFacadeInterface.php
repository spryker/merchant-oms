<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantOms\Business;

use Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer;
use Generated\Shared\Transfer\MerchantOrderItemCriteriaTransfer;

interface MerchantOmsFacadeInterface
{
    /**
     * Specification:
     * - Requires MerchantOmsTriggerRequest.merchantOrderItems transfer field to be set.
     * - Requires MerchantOmsTriggerRequest.merchant.merchantReference transfer field to be set.
     * - Finds merchant state machine process by merchant reference, uses default process name from configuration as a fallback.
     * - Dispatches an initial merchant OMS event of merchant state machine process for each merchant order item.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer
     *
     * @return void
     */
    public function triggerForNewMerchantOrderItems(MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer): void;

    /**
     * Specification:
     * - Requires MerchantOmsTriggerRequest.merchantOrderItems transfer field to be set.
     * - Requires MerchantOmsTriggerRequest.merchantOmsEventName transfer field to be set.
     * - Dispatches a merchant OMS event for each merchant order item.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer
     *
     * @return void
     */
    public function triggerEventForMerchantOrderItems(MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer): void;

    /**
     * Specification:
     * - Finds merchant order items with provided criteria.
     * - Requires MerchantOrderItemCriteria.stateMachineItemStateIds transfer field to be set.
     * - Returns array of StateMachineItem transfers with identifier(id of merchant order item) and idItemState.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantOrderItemCriteriaTransfer $merchantOrderItemCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\StateMachineItemTransfer[]
     */
    public function getStateMachineItemsByCriteria(MerchantOrderItemCriteriaTransfer $merchantOrderItemCriteriaTransfer): array;
}
