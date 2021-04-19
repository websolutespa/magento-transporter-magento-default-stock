<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterMagentoDefaultStock\Model\ResourceModel;

use Magento\Framework\Exception\NoSuchEntityException;

interface SetDefaultStockInterface
{
    /**
     * @param int $productId
     * @param float $quantity
     * @param int $stockId
     * @param int $status
     * @return void
     * @throws NoSuchEntityException
     */
    public function execute(int $productId, float $quantity, int $stockId, int $status): void;

    /**
     * @param int $productId
     * @param int $stockId
     * @param int $status
     */
    public function setStock(int $productId, int $stockId, int $status): void;
}
