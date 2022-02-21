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

namespace Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Edit;

/**
 * Class Tabs
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml\Attribute\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }

    /**
     * @inheritdoc
     */
    protected function _beforeToHtml()
    {
        $this->addTab('main', [
            'label'   => __('Properties'),
            'title'   => __('Properties'),
            'content' => $this->getChildHtml('main'),
            'active'  => true
        ]);
        $this->addTab('labels', [
            'label'   => __('Manage Labels'),
            'title'   => __('Manage Labels'),
            'content' => $this->getChildHtml('labels')
        ]);
        $this->addTab('front', [
            'label'   => __('Storefront Properties'),
            'title'   => __('Storefront Properties'),
            'content' => $this->getChildHtml('front')
        ]);

        return parent::_beforeToHtml();
    }
}
