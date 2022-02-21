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

use Mageplaza\CustomerAttributes\Helper\Data;

/**
 * Class File
 * @package Mageplaza\CustomerAttributes\Block\Form\Renderer
 */
class File extends AbstractRenderer
{
    /**
     * Return escaped value
     *
     * @return string
     */
    public function getEscapedValue()
    {
        $value = Data::jsonDecode($this->getValue());
        $file  = is_array($value) && isset($value['file']) ? $value['file'] : $this->getValue();

        if ($file) {
            return $this->escapeHtml($this->urlEncoder->encode($file));
        }

        return '';
    }

    /**
     * @param $type
     *
     * @return string
     */
    public function getFilePath($type)
    {
        $param = '/' . $type . '/' . $this->getEscapedValue() . '/type/' . $this->_entityType->getEntityTypeCode();

        return $this->_urlBuilder->getUrl('mpca/viewfile/index' . $param);
    }
}
