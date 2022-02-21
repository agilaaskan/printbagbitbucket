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

namespace Mageplaza\CustomerAttributes\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Mageplaza\CustomerAttributes\Helper\Data;

/**
 * Class InputType
 * @package Mageplaza\CustomerAttributes\Model\Config\Source
 */
class InputType implements ArrayInterface
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @param Data $dataHelper
     */
    public function __construct(Data $dataHelper)
    {
        $this->dataHelper = $dataHelper;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $inputTypes = $this->dataHelper->getInputType();
        $options    = [];
        foreach ($inputTypes as $key => $value) {
            $options[] = ['value' => $key, 'label' => $value['label']];
        }

        return $options;
    }
}
