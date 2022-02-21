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

namespace Mageplaza\CustomerAttributes\Block\Form\Renderer;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class SelectVisual
 * @package Mageplaza\CustomerAttributes\Block\Form\Renderer
 */
class SelectVisual extends AbstractRenderer
{
    /**
     * Return array of select options
     *
     * @return array
     * @throws LocalizedException
     */
    public function getOptions()
    {
        return $this->getAttributeObject()->getSource()->getAllOptions(false);
    }

    /**
     * @param $value
     *
     * @return string
     * @throws LocalizedException
     */
    public function getMedia($value)
    {
        $optionId   = $this->getAttributeObject()->getSource()->getOptionId($value);
        $collection = $this->swatchCollection->create()->addFieldToFilter('option_id', $optionId);
        $item       = $collection->getFirstItem();

        switch ($item->getType()) {
            case 1:
                return '<div class="color" style="background-color: ' . $item->getValue() . '"></div>';
            case 2:
                return '<img class="image" src="' . $this->swatchHelper->getSwatchAttributeImage(
                    'swatch_thumb',
                    $item->getValue()
                ) . '">';
            default:
                return '';
        }
    }
}
