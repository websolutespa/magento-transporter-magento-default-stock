<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterMagentoDefaultStock\Model\ResourceModel;

use Magento\Framework\App\ResourceConnection;

class GetStockList
{
    const TABLE_NAME = 'cataloginventory_stock';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        $tableName = $this->resourceConnection->getTableName(self::TABLE_NAME);
        $connection = $this->resourceConnection->getConnection();
        $query = $connection->select()
            ->from($tableName);
        return $connection->fetchAll($query);
    }
}
