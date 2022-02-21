<?php
namespace Mconnect\Csproduct\Block\Adminhtml\Customer\Edit;

use Magento\Ui\Component\Layout\Tabs\TabWrapper;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;

class Tabs extends \Magento\Backend\Block\Widget\Grid\Extended implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    
    protected function _beforeToHtml()
    {
        
        $this->addTab(
            'faq_products',
            [
                'label' => __('Products'),
                'title' => __('Products'),
                'url' => $this->getUrl('csproduct/ * /customerproduct', ['_current' => true]),
                'class' => 'ajax'
            ]
        );
        return parent::_beforeToHtml();
    }
    
    public function canShowTab()
    {
        
        return true;
    }
    
    public function getTabLabel()
    {
        return __('Customer Specific Products');
    }
    
    
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Customer Specific Products');
    }
    
    
    
     /**
      * @return bool
      */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }
    
    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }
    
    
    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    
    public function getTabUrl()
    {
        return $this->getUrl('csproduct/*/customerproduct', ['_current' => true]);
    }
    
     /**
      * Tab should be loaded trough Ajax call
      *
      * @return bool
      */
    public function isAjaxLoaded()
    {
        return true;
    }
}
