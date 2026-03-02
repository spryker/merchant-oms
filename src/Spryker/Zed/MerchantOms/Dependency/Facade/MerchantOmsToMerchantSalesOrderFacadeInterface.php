<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantOms\Dependency\Facade;

use Generated\Shared\Transfer\MerchantOrderCriteriaTransfer;
use Generated\Shared\Transfer\MerchantOrderItemCriteriaTransfer;
use Generated\Shared\Transfer\MerchantOrderItemResponseTransfer;
use Generated\Shared\Transfer\MerchantOrderItemTransfer;
use Generated\Shared\Transfer\MerchantOrderTransfer;

interface MerchantOmsToMerchantSalesOrderFacadeInterface
{
    public function updateMerchantOrderItem(MerchantOrderItemTransfer $merchantOrderItemTransfer): MerchantOrderItemResponseTransfer;

    public function findMerchantOrderItem(MerchantOrderItemCriteriaTransfer $merchantOrderItemCriteriaTransfer): ?MerchantOrderItemTransfer;

    public function findMerchantOrder(MerchantOrderCriteriaTransfer $merchantCriteriaTransfer): ?MerchantOrderTransfer;
}
