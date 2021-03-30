<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterMagentoDefaultStock\Model\ResourceModel;

use Magento\CatalogInventory\Api\Data\StockItemInterfaceFactory;
use Magento\CatalogInventory\Model\ResourceModel\Stock\Item;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Model\AbstractModel;

class SetDefaultStock implements SetDefaultStockInterface
{
    /**
     * @var StockItemInterfaceFactory
     */
    private $stockItemInterfaceFactory;

    /**
     * @var Item
     */
    private $stockItemResourceModel;

    /**
     * @param StockItemInterfaceFactory $stockItemInterfaceFactory
     * @param Item $stockItemResourceModel
     */
    public function __construct(
        StockItemInterfaceFactory $stockItemInterfaceFactory,
        Item $stockItemResourceModel
    ) {
        $this->stockItemInterfaceFactory = $stockItemInterfaceFactory;
        $this->stockItemResourceModel = $stockItemResourceModel;
    }

    /**
     * @inheritDoc
     */
    public function execute(int $productId, float $quantity, int $stockId, int $status): void
    {
        $stockItem = $this->stockItemInterfaceFactory->create();
        $this->stockItemResourceModel->loadByProductId($stockItem, $productId, $stockId);
        $stockItem->setQty($quantity);

        $stockItem->setIsInStock($quantity > 0);

        /** @var AbstractModel $stockItem */
        $this->stockItemResourceModel->save($stockItem);
    }
}
