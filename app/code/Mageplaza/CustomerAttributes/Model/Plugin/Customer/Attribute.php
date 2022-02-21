<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomerAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomerAttributes\Model\Plugin\Customer;

use Exception;
use Magento\Customer\Model\Customer;
use Magento\Framework\Indexer\IndexerRegistry;

/**
 * Class Attribute
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Customer
 */
class Attribute
{
    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * Attribute constructor.
     *
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(IndexerRegistry $indexerRegistry)
    {
        $this->indexerRegistry = $indexerRegistry;
    }

    /**
     * @param \Magento\Customer\Model\Attribute $subject
     *
     * @return void
     * @throws Exception
     */
    public function afterInvalidate(\Magento\Customer\Model\Attribute $subject)
    {
        $indexer = $this->indexerRegistry->get(Customer::CUSTOMER_GRID_INDEXER_ID);
        $indexer->reindexAll();
    }
}
