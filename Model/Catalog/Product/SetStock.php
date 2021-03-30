<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterMagentoDefaultStock\Model\Catalog\Product;

use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use Magento\CatalogInventory\Model\Indexer\Stock\Action\Row;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Websolute\TransporterBase\Exception\TransporterException;
use Websolute\TransporterMagentoDefaultStock\Model\ResourceModel\SetDefaultStockInterface;

class SetStock implements SetStockInterface
{
    /**
     * @var SetDefaultStockInterface
     */
    private $setDefaultStock;

    /**
     * @var ProductFactory
     */
    private $productFactory;

    /**
     * @var Row
     */
    private $indexerRow;

    /**
     * @param SetDefaultStockInterface $setDefaultStock
     * @param ProductFactory $productFactory
     * @param Row $indexerRow
     */
    public function __construct(
        SetDefaultStockInterface $setDefaultStock,
        ProductFactory $productFactory,
        Row $indexerRow
    ) {
        $this->productFactory = $productFactory;
        $this->indexerRow = $indexerRow;
        $this->setDefaultStock = $setDefaultStock;
    }

    /**
     * @param string $sku
     * @param float $quantity
     * @param int $stockId
     * @param bool $reindex
     * @throws TransporterException
     */
    public function execute(string $sku, float $quantity, int $stockId, bool $reindex = false)
    {
        $productId = $this->getProductIdFromSku($sku);

        try {
            $this->setDefaultStock->execute(
                $productId,
                $quantity,
                $stockId,
                StockStatusInterface::STATUS_IN_STOCK
            );
        } catch (NoSuchEntityException $e) {
            throw new TransporterException(__($e->getMessage()));
        }

        if ($reindex) {
            try {
                $this->indexerRow->execute($productId);
            } catch (LocalizedException $e) {
                throw new TransporterException(__($e->getMessage()));
            }
        }
    }

    /**
     * @param string $sku
     * @return int
     * @throws TransporterException
     */
    private function getProductIdFromSku(string $sku): int
    {
        $product = $this->productFactory->create();
        $product = $product->loadByAttribute('sku', $sku, 'entity_id');
        if (!$product) {
            throw new TransporterException(__('Product with sku %2 does not exist', 'sku', $sku));
        }
        return (int)$product->getId();
    }
}
