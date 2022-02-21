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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Sales\ViewModel;

use Magento\Eav\Model\Config;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\CustomerAttributes\Helper\Data;

/**
 * Class AddressFormatter
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Sales\ViewModel
 */
class AddressFormatter
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
     * @param \Magento\Sales\ViewModel\Customer\AddressFormatter $subject
     * @param string $result
     *
     * @return string
     * @throws LocalizedException
     */
    public function afterGetAddressesJson(\Magento\Sales\ViewModel\Customer\AddressFormatter $subject, $result)
    {
        $data = Data::jsonDecode($result);

        foreach ($data as &$address) {
            foreach ($address as $key => &$value) {
                if (!($value === false || (is_array($value) && count($value)))) {
                    continue;
                }

                $attribute = $this->eavConfig->getAttribute('customer_address', $key);

                if ($defaultValue = $attribute->getDefaultValue()) {
                    switch ($attribute->getFrontendInput()) {
                        case 'multiselect':
                        case 'multiselect_visual':
                            $value = explode(',', $defaultValue);
                            break;
                        default:
                            $value = $defaultValue;
                    }
                }
            }
        }

        unset($address, $value);

        return Data::jsonEncode($data);
    }
}
