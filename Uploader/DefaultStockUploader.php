<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterMagentoDefaultStock\Uploader;

use Monolog\Logger;
use Websolute\TransporterBase\Api\UploaderInterface;
use Websolute\TransporterBase\Exception\TransporterException;
use Websolute\TransporterEntity\Api\EntityRepositoryInterface;
use Websolute\TransporterImporter\Model\DotConvention;
use Websolute\TransporterMagentoDefaultStock\Api\DefaultStockConfigInterface;
use Websolute\TransporterMagentoDefaultStock\Model\Catalog\Product\SetStockInterface;

class DefaultStockUploader implements UploaderInterface
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var EntityRepositoryInterface
     */
    private $entityRepository;

    /**
     * @var DefaultStockConfigInterface
     */
    private $config;

    /**
     * @var SetStockInterface
     */
    private $setStock;

    /**
     * @var DotConvention
     */
    private $dotConvention;

    /**
     * @var string
     */
    private $field;

    /**
     * @param Logger $logger
     * @param EntityRepositoryInterface $entityRepository
     * @param DefaultStockConfigInterface $config
     * @param SetStockInterface $setStock
     * @param DotConvention $dotConvention
     * @param string $field
     */
    public function __construct(
        Logger $logger,
        EntityRepositoryInterface $entityRepository,
        DefaultStockConfigInterface $config,
        SetStockInterface $setStock,
        DotConvention $dotConvention,
        string $field
    ) {
        $this->logger = $logger;
        $this->entityRepository = $entityRepository;
        $this->config = $config;
        $this->setStock = $setStock;
        $this->dotConvention = $dotConvention;
        $this->field = $field;
    }

    /**
     * @param int $activityId
     * @param string $uploaderType
     * @throws TransporterException
     */
    public function execute(int $activityId, string $uploaderType): void
    {
        $allActivityEntities = $this->entityRepository->getAllDataManipulatedByActivityIdGroupedByIdentifier($activityId);

        $i = 0;
        $tot = count($allActivityEntities);
        foreach ($allActivityEntities as $entityIdentifier => $entities) {
            $this->logger->info(__(
                'activityId:%1 ~ Uploader ~ uploaderType:%2 ~ entityIdentifier:%3 ~ step:%4/%5 ~ START',
                $activityId,
                $uploaderType,
                $entityIdentifier,
                ++$i,
                $tot
            ));

            try {
                $sku = $entityIdentifier;
                $quantity = (float)$this->dotConvention->getValue($entities, $this->field);
                $stockId = $this->config->getStockId();
                $reindex = $this->config->isReindexAfterImport();

                $this->setStock->execute($sku, $quantity, $stockId, $reindex);

                $this->logger->info(__(
                    'activityId:%1 ~ Uploader ~ uploaderType:%2 ~ entityIdentifier:%3 ~ new stock value:%4 ~ END',
                    $activityId,
                    $uploaderType,
                    $entityIdentifier,
                    $quantity
                ));
            } catch (TransporterException $e) {
                $this->logger->error(__(
                    'activityId:%1 ~ Uploader ~ uploaderType:%2 ~ entityIdentifier:%3 ~ ERROR ~ error:%4',
                    $activityId,
                    $uploaderType,
                    $entityIdentifier,
                    $e->getMessage()
                ));

                if (!$this->config->continueInCaseOfErrors()) {
                    throw new TransporterException(__(
                        'activityId:%1 ~ Uploader ~ uploaderType:%2 ~ entityIdentifier:%3 ~ END ~ Because of continueInCaseOfErrors = false',
                        $activityId,
                        $uploaderType,
                        $entityIdentifier
                    ));
                }
            }
        }
    }
}
