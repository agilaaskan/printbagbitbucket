<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerBR\Plugin\Magento\Checkout\Block\Checkout\LayoutProcessor;

use Magento\Checkout\Block\Checkout\LayoutProcessor;

class AddRequiredValidationToAllStreetLines
{
    /**
     * @param LayoutProcessor $subject
     * @param array $result
     * @return array
     */
    public function afterProcess(LayoutProcessor $subject, $result)
    {
        $result = $this->addValidationOnShippingAddressStreetFields($result);
        $result = $this->addValidationOnCheckmoBillingAddressStreetFields($result);

        return $result;
    }

    /**
     * @param $result
     * @return array
     */
    private function addValidationOnShippingAddressStreetFields($result)
    {
        if (!isset($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'])
        ) {
            return $result;
        }

        foreach ($result['components']['checkout']['children']['steps']['children']['shipping-step']['children']
                 ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'] as &$child
        ) {
            if (!isset($child['validation']) || !is_array($child['validation'])) {
                continue;
            }

            $child['validation']['required-entry'] = true;
        }

        return $result;
    }

    /**
     * @param $result
     * @return array
     */
    private function addValidationOnCheckmoBillingAddressStreetFields($result)
    {
        if (!isset($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children']['checkmo-form']['children']['form-fields']
            ['children']['street']['children'])
        ) {
            return $result;
        }

        foreach ($result['components']['checkout']['children']['steps']['children']['billing-step']['children']
                 ['payment']['children']['payments-list']['children']['checkmo-form']['children']['form-fields']
                 ['children']['street']['children'] as &$child
        ) {
            if (!isset($child['validation']) || !is_array($child['validation'])) {
                continue;
            }

            $child['validation']['required-entry'] = true;
        }

        return $result;
    }
}
