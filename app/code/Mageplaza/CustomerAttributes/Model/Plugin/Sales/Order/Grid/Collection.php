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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Sales\Order\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderCollection;

/**
 * Class DataProvider
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Sales
 */
class Collection
{
    /**
     * @param OrderCollection $subject
     * @param $result
     *
     * @return mixed
     */
    public function afterGetSelect(
        OrderCollection $subject,
        $result
    ) {
        if ($result && !$subject->getFlag('is_mageplaza_customer_attribute_sales_order_joined')) {
            $table = $subject->getResource()->getTable('mageplaza_customer_attribute_sales_order');
            $result->joinLeft(
                ['mp_customer_order_attributes' => $table],
                'mp_customer_order_attributes.order_id = main_table.entity_id'
            );
            $tableDescription = $subject->getConnection()->describeTable($table);
            foreach ($tableDescription as $columnInfo) {
                $subject->addFilterToMap(
                    $columnInfo['COLUMN_NAME'],
                    'mp_customer_order_attributes.' . $columnInfo['COLUMN_NAME']
                );
            }
            $subject->setFlag('is_mageplaza_customer_attribute_sales_order_joined', true);
        }

        return $result;
    }
}
