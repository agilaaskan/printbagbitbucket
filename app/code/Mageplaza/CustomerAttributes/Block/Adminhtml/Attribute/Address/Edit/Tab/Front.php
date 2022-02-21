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

namespace Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Address\Edit\Tab;

use Magento\Framework\Data\Form;

/**
 * Class Front
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Address\Edit\Tab
 */
class Front extends \Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Edit\Tab\Front
{
    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var Form $form */
        $form = $this->getForm();

        $form->getElement('used_in_forms')->setValues($this->dataHelper->getAddressFormOptions());
        $form->getElement('used_in_forms')->setSize(5);

        return $this;
    }
}
