<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterMagentoDefaultStock\Api;

use Websolute\TransporterBase\Api\TransporterConfigInterface;

interface DefaultStockConfigInterface extends TransporterConfigInterface
{
    /**
     * @return int
     */
    public function getStockId(): int;

    /**
     * @return bool
     */
    public function isReindexAfterImport(): bool;
}
