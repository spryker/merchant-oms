<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Spryker Marketplace License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\MerchantOms\Communication\FilePathResolver;

use Generated\Shared\Transfer\FilePathResolverResponseTransfer;

interface FilePathResolverInterface
{
    public function resolveFilePath(string $filePath): FilePathResolverResponseTransfer;
}
