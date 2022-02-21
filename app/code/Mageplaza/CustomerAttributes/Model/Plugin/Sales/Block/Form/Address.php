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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Sales\Block\Form;

use Magento\Eav\Model\Config;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Address
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Sales\Block\Form
 */
class Address
{
    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * AddressFormatter constructor.
     *
     * @param Config $eavConfig
     */
    public function __construct(Config $eavConfig)
    {
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param \Magento\Sales\Block\Adminhtml\Order\Create\Form\Address $subject
     * @param array $result
     *
     * @return array
     * @throws LocalizedException
     */
    public function afterGetFormValues(\Magento\Sales\Block\Adminhtml\Order\Create\Form\Address $subject, $result)
    {
        foreach ($result as $key => &$value) {
            if ($value !== null) {
                continue;
            }

            $attribute = $this->eavConfig->getAttribute('customer_address', $key);

            if ($defaultValue = $attribute->getDefaultValue()) {
                $value = $defaultValue;
            }
        }

        unset($address, $value);

        return $result;
    }
}
