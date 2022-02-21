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

use Magento\Customer\Model\Attribute;
use Magento\Framework\Data\Form;

/**
 * Class Depend
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Address\Edit\Tab
 */
class Depend extends \Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Edit\Tab\Depend
{
    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();

        /** @var Attribute $attributeObject */
        $attributeObject = $this->getAttributeObject();

        /** @var Form $form */
        $form = $this->getForm();

        $dependFields = [['value' => '', 'label' => __('None')]];
        $dependValues = [];

        /** @var Attribute $attribute */
        foreach ($this->addressAttributeCollection as $attribute) {
            if ($attribute->getId() == $attributeObject->getId()) {
                continue;
            }

            if (in_array($attribute->getFrontendInput(), ['select', 'select_visual'])) {
                $dependFields[] = [
                    'value' => $attribute->getId(),
                    'label' => $attribute->getDefaultFrontendLabel()
                ];

                foreach ($attribute->getOptions() as $option) {
                    if ($option->getLabel() && $option->getValue()) {
                        $dependValues[] = [
                            'value' => $attribute->getId() . '_' . $option->getValue(),
                            'label' => $option->getLabel()
                        ];
                    }
                }
            }
        }

        $form->getElement('field_depend')->setValues($dependFields);
        $form->getElement('value_depend')->setValues($dependValues);

        return $this;
    }
}
