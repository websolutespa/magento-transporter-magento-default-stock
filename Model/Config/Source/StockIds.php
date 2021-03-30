<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterMagentoDefaultStock\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Websolute\TransporterMagentoDefaultStock\Model\ResourceModel\GetStockList;

class StockIds implements OptionSourceInterface
{
    /**
     * @var GetStockList
     */
    private $getStockList;

    public function __construct(
        GetStockList $getStockList
    ) {
        $this->getStockList = $getStockList;
    }

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $stocks = $this->getStockList->execute();
        $result = [];
        foreach ($stocks as $stock) {
            $result[] = ['value' => $stock['stock_id'], 'label' => $stock['stock_name']];
        }
        return $result;
    }
}
