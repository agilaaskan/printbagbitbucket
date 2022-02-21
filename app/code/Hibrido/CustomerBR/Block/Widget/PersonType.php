<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerBR\Block\Widget;

use Hibrido\CustomerBR\Model\Source\Config\PersonType as PersonTypeSource;
use Magento\Customer\Block\Form\Edit;
use Magento\Customer\Block\Form\Register;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

class PersonType extends Template
{
    /**
     * @var PersonTypeSource
     */
    private $personTypeSource;

    /**
     * @param Template\Context $context
     * @param PersonTypeSource $personTypeSource
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        PersonTypeSource $personTypeSource,
        array $data = []
    ) {
        $this->personTypeSource = $personTypeSource;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('Hibrido_CustomerBR::widget/person-type.phtml');
        $this->initHbPersonType();
    }

    /**
     * @return array
     */
    public function getPersonTypeOptions()
    {
        return $this->personTypeSource->getAllOptions();
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
                $this->setHbPersonType($formData->getHbPersonType());
                return;
            }
        }

        //Try to get value from edit customer block.
        /** @var Edit $editBlock */
        if ($editBlock = $layout->getBlock('customer_edit')) {
            if ($customer = $editBlock->getCustomer()) {
                if ($attribute = $customer->getCustomAttribute('hb_person_type')) {
                    $this->setHbPersonType($attribute->getValue());
                    return;
                }
            }
        }
    }
}
