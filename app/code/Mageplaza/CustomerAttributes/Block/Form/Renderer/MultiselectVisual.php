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

/**
 * Class MultiselectVisual
 * @package Mageplaza\CustomerAttributes\Block\Form\Renderer
 */
class MultiselectVisual extends SelectVisual
{
    /**
     * Check is value selected
     *
     * @param string $value
     *
     * @return boolean
     */
    public function isValueSelected($value)
    {
        return in_array($value, explode(',', $this->getValue()));
    }
}
