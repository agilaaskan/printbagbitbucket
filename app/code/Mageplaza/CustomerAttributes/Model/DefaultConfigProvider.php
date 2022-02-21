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

namespace Mageplaza\CustomerAttributes\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Swatches\Model\Swatch;
use Mageplaza\CustomerAttributes\Helper\Data;
use Zend_Serializer_Exception;

/**
 * Class DefaultConfigProvider
 * @package Mageplaza\CustomerAttributes\Model
 */
class DefaultConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * DefaultConfigProvider constructor.
     *
     * @param Data $dataHelper
     */
    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        if (!$this->dataHelper->isModuleOutputEnabled('Mageplaza_CustomerAttributes')) {
            return [];
        }

        $output = [
            'mpCaConfig' => $this->getMpCaConfig()
        ];

        return $output;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    private function getMpCaConfig()
    {
        return [
            'customerAttributes' => [
                'dependency'    => $this->getAttributeDataDependency(),
                'contentType'   => $this->getAttributeDataContentType(),
                'tinymceConfig' => $this->dataHelper->getTinymceConfig()
            ]
        ];
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    private function getAttributeDataDependency()
    {
        $attributes = $this->dataHelper->getAttributeWithFilters('customer_address', 'checkout_index_index');

        $data = [];
        foreach ($attributes as $attribute) {
            if ($attribute->getFieldDepend() || $attribute->getFrontendInput() == 'select') {
                $data[] = $attribute->getData();
            }
        }

        return $data;
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    private function getAttributeDataContentType()
    {
        $attributes = $this->dataHelper->getAttributeWithFilters('customer_address', 'checkout_index_index');

        $data = [];
        foreach ($attributes as $attribute) {
            if ($attribute->getFrontendInput() == 'textarea') {
                $additionalData = $this->dataHelper->getAdditionalData($attribute);
                if (!empty($additionalData[Swatch::SWATCH_INPUT_TYPE_KEY])) {
                    $data[] = $attribute->getData();
                }
            }
        }

        return $data;
    }
}
