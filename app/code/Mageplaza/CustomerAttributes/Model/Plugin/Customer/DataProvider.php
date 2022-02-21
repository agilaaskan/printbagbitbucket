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

use Mageplaza\CustomerAttributes\Model\Address\OrderFactory;

/**
 * Class DataProvider
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Customer
 */
class DataProvider
{
    /**
     * @var OrderFactory
     */
    protected $orderAddressFactory;

    /**
     * @param OrderFactory $orderAddressFactory
     */
    public function __construct(OrderFactory $orderAddressFactory)
    {
        $this->orderAddressFactory = $orderAddressFactory;
    }

    /**
     * @param \Magento\Customer\Ui\Component\DataProvider $subject
     * @param array $result
     *
     * @return array
     */
    public function afterGetData(\Magento\Customer\Ui\Component\DataProvider $subject, $result)
    {
        if (isset($result['items'])) {
            $orderAddress = $this->orderAddressFactory->create();
            $data         = $orderAddress->attachDataToCustomerAddress($result['items'], 'billing_');

            foreach ($result['items'] as $index => &$item) {
                foreach ($item as $key => &$value) {
                    if (isset($data[$item['entity_id']][$key]) && strpos($key, 'billing_') !== false) {
                        $value = $data[$item['entity_id']][$key];
                    }
                }
            }
        }

        return $result;
    }
}
