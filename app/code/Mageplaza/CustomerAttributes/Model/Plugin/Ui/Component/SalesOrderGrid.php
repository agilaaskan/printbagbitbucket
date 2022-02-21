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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Ui\Component;

use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\Listing\Columns;
use Mageplaza\CustomerAttributes\Model\ResourceModel\Attribute\CollectionFactory;
use Mageplaza\CustomerAttributes\Ui\Component\ColumnFactory;

/**
 * Class SalesOrderGrid
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Ui\Component
 */
class SalesOrderGrid
{
    /**
     * Default columns max order
     */
    protected const DEFAULT_COLUMNS_MAX_ORDER = 100;

    /**
     * @var ColumnFactory
     */
    protected $columnFactory;

    /**
     * @var CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * SalesOrderGrid constructor.
     *
     * @param ColumnFactory $columnFactory
     * @param CollectionFactory $attributeCollectionFactory
     */
    public function __construct(
        ColumnFactory $columnFactory,
        CollectionFactory $attributeCollectionFactory
    ) {
        $this->columnFactory              = $columnFactory;
        $this->attributeCollectionFactory = $attributeCollectionFactory;
    }

    /**
     * @return array
     */
    protected function getAttributeList()
    {
        $attributes = $this->attributeCollectionFactory->create();

        $result = [];
        foreach ($attributes as $attribute) {
            if (!$attribute->getIsUsedInSalesOrderGrid()) {
                continue;
            }

            $result[] = $attribute;
        }

        return $result;
    }

    /**
     * @param Columns $subject
     *
     * @throws LocalizedException
     */
    public function beforePrepare(Columns $subject)
    {
        if ($subject->getName() === 'sales_order_columns') {
            $columnSortOrder = self::DEFAULT_COLUMNS_MAX_ORDER;

            foreach ($this->getAttributeList() as $attribute) {
                $config = [];
                if (!$subject->getComponent($attribute->getAttributeCode())) {
                    $attribute->setAttributeCode('customer_' . $attribute->getAttributeCode());
                    $config['sortOrder'] = ++$columnSortOrder;
                    if ($attribute->getIsFilterableInGrid()) {
                        $config['filter'] = $subject->getFilterType($attribute->getFrontendInput());
                    }
                    $column = $this->columnFactory->create($attribute, $subject->getContext(), $config);
                    $column->prepare();
                    $subject->addComponent($attribute->getAttributeCode(), $column);
                }
            }
        }
    }
}
