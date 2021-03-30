<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterMagentoDefaultStock\Model\Catalog\Product;

use Websolute\TransporterBase\Exception\TransporterException;

interface SetStockInterface
{
    /**
     * @param string $sku
     * @param float $quantity
     * @param int $stockId
     * @param bool $reindex
     * @throws TransporterException
     */
    public function execute(string $sku, float $quantity, int $stockId, bool $reindex = false);
}
