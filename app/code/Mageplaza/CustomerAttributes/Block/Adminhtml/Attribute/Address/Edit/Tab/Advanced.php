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
use Magento\Framework\Data\Form\Element\Fieldset;

/**
 * Class Advanced
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Address\Edit\Tab
 */
class Advanced extends \Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Edit\Tab\Advanced
{
    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var Form $form */
        $form = $this->getForm();

        /** @var Fieldset $fieldset */
        $fieldset = $form->getElement('advanced_fieldset');
        $fieldset->removeField('is_used_in_sales_order_grid');

        return $this;
    }
}
