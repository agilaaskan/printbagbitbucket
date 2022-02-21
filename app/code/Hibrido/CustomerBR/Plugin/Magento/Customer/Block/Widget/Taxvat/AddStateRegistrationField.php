<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerBR\Plugin\Magento\Customer\Block\Widget\Taxvat;

use Hibrido\CustomerBR\Block\Widget\StateRegistration;
use Magento\Customer\Block\Widget\Taxvat;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\LayoutInterface;

class AddStateRegistrationField
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @param LayoutInterface $layout
     * @param Manager $moduleManager
     */
    public function __construct(LayoutInterface $layout, Manager $moduleManager)
    {
        $this->layout = $layout;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param Taxvat $subject
     * @param string $result
     * @return string
     */
    public function afterToHtml(Taxvat $subject, $result)
    {
        //If is Magento Commerce and the Magento Customer Custom
        //Attributes module is enable, we don't create our block
        //because it will cause duplicated fields.

        if ($this->moduleManager->isEnabled('Magento_CustomerCustomAttributes')) {
            return $result;
        }

        return $result . $this->layout->createBlock(StateRegistration::class)->toHtml();
    }
}
