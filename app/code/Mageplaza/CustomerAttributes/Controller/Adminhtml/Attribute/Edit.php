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

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\Result\Redirect;
use Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute;

/**
 * Class Edit
 * @package Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute
 */
class Edit extends Attribute
{
    /**
     * @var string
     */
    protected $type = 'customer';

    /**
     * @return Page|Redirect
     */
    public function execute()
    {
        $attributeObject = $this->_initAttribute();

        $attributeId = $this->getRequest()->getParam('id');
        if ($attributeId) {
            $attributeObject->load($attributeId);

            if (!$attributeObject->getId()) {
                $this->messageManager->addErrorMessage(__('This attribute no longer exists.'));

                return $this->returnResult('customer/*/', []);
            }
        }

        // restore attribute data
        $data = $this->_session->getAttributeData(true);
        if (!empty($data)) {
            $attributeObject->addData($data);
        }

        $this->_coreRegistry->register('entity_attribute', $attributeObject);

        $label     = $attributeObject->getDefaultFrontendLabel();
        $label     = is_array($label) ? $label[0] : $label;
        $pageTitle = $attributeId ? $label : __('New %1 Attribute', ucwords(str_replace('_', ' ', $this->type)));

        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);

        return $resultPage;
    }
}
