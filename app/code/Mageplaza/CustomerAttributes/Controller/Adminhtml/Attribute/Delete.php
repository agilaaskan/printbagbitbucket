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

namespace Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute;

/**
 * Class Delete
 * @package Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute
 */
class Delete extends Attribute
{
    /**
     * @var string
     */
    protected $type = 'customer';

    /**
     * @return Redirect
     */
    public function execute()
    {
        $attributeId = $this->getRequest()->getParam('id');
        if ($attributeId) {
            $attributeObject = $this->_initAttribute()->load($attributeId);
            try {
                $attributeObject->delete();

                $this->_eventManager->dispatch(
                    'mageplaza_' . $this->type . '_attribute_delete',
                    ['attribute' => $attributeObject]
                );

                $this->messageManager->addSuccessMessage(__('You deleted the attribute.'));

                return $this->returnResult('customer/*/', []);
            } catch (Exception $e) {
                $this->messageManager->addExceptionMessage(
                    $e,
                    __('We can\'t delete the attribute right now.')
                );

                return $this->returnResult('customer/*/edit', ['id' => $attributeId, '_current' => true]);
            }
        }

        $this->messageManager->addErrorMessage(__('We can\'t find an attribute to delete.'));

        return $this->returnResult('customer/*/', []);
    }
}
