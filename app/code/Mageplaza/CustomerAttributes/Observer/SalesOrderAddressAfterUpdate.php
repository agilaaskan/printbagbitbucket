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

namespace Mageplaza\CustomerAttributes\Observer;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order\Address as AddressModel;
use Mageplaza\CustomerAttributes\Model\Address\OrderFactory;

/**
 * Class SalesOrderAddressAfterUpdate
 * @package Mageplaza\CustomerAttributes\Observer
 */
class SalesOrderAddressAfterUpdate implements ObserverInterface
{
    /**
     * @var OrderFactory
     */
    protected $orderAddressFactory;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param OrderFactory $orderAddressFactory
     * @param ObjectManagerInterface $objectManager
     * @param RequestInterface $request
     */
    public function __construct(
        OrderFactory $orderAddressFactory,
        ObjectManagerInterface $objectManager,
        RequestInterface $request
    ) {
        $this->orderAddressFactory = $orderAddressFactory;
        $this->request             = $request;
        $this->objectManager       = $objectManager;
    }

    /**
     * After update observer for order address
     *
     * @param Observer $observer
     *
     * @return $this
     */
    public function execute(Observer $observer)
    {
        $data      = $this->request->getPostValue();
        $addressId = $this->request->getParam('address_id');

        /** @var $address OrderAddressInterface|AddressModel */
        $address = $this->objectManager->create(OrderAddressInterface::class)->load($addressId);

        foreach ($data as $key => &$datum) {
            if (is_array($datum)) {
                $datum = implode(',', $datum);
            }
        }

        $address->addData($data);

        if ($address instanceof AbstractModel) {
            $orderAddressModel = $this->orderAddressFactory->create();
            $orderAddressModel->saveAttributeData($address);
        }

        return $this;
    }
}
