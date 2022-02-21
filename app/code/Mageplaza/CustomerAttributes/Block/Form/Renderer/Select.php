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
 * Class Select
 * @package Mageplaza\CustomerAttributes\Block\Form\Renderer
 */
class Select extends AbstractRenderer
{
    /**
     * @return array
     * @throws LocalizedException
     */
    public function getOptions()
    {
        $options = $this->getAttributeObject()->getSource()->getAllOptions(false);

        array_unshift($options, ['label' => __('Please select an option'), 'value' => '']);

        return $options;
    }
}
