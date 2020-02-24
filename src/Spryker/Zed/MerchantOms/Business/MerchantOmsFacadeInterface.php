<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantOms\Business;

use Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer;

interface MerchantOmsFacadeInterface
{
    /**
     * Specification:
     * - Requires MerchantOmsTriggerRequest.merchantOrderItems.
     * - Requires MerchantOmsTriggerRequest.merchant.merchantReference.
     * - Tries to find merchant state machine process, if not found takes process name from config.
     * - Dispatches an initial merchant oms event of merchant state machine process for each merchant order item.
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
     * - Requires MerchantOmsTriggerRequest.merchantOrderItems.
     * - Requires MerchantOmsTriggerRequest.merchantOmsEventName.
     * - Dispatches a merchant oms event for each merchant order item.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer
     *
     * @return void
     */
    public function triggerEventForMerchantOrderItems(MerchantOmsTriggerRequestTransfer $merchantOmsTriggerRequestTransfer): void;
}
