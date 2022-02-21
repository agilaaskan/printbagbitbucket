<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerBR\Block\Widget;

use Magento\Customer\Block\Form\Edit;
use Magento\Customer\Block\Form\Register;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

class StateRegistration extends Template
{
    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Hibrido_CustomerBR::widget/state-registration.phtml');
        $this->initHbPersonType();
    }

    /**
     * @return void
     * @noinspection PhpUndefinedMethodInspection
     */
    private function initHbPersonType()
    {
        try {
            $layout = $this->getLayout();
        } catch (LocalizedException $e) {
            return;
        }

        //Try to get value from register block.
        /** @var Register $registerBlock */
        if ($registerBlock = $layout->getBlock('customer_form_register')) {
            if ($formData = $registerBlock->getFormData()) {
                $this->setHbStateRegistration($formData->getHbStateRegistration());
                return;
            }
        }

        //Try to get value from edit customer block.
        /** @var Edit $editBlock */
        if ($editBlock = $layout->getBlock('customer_edit')) {
            if ($customer = $editBlock->getCustomer()) {
                if ($attribute = $customer->getCustomAttribute('hb_state_registration')) {
                    $this->setHbStateRegistration($attribute->getValue());
                    return;
                }
            }
        }
    }
}
