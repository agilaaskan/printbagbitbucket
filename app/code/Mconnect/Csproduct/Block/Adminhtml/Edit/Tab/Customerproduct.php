<?php
/**
 * @copyright Copyright (c) 2016 www.magebuzz.com
 */
namespace Mconnect\Csproduct\Block\Adminhtml\Edit\Tab;
 
use Magento\Ui\Component\Layout\Tabs\TabWrapper;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;

class Customerproduct extends Generic implements TabInterface
{
    
    /*
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    
    
    
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
  
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }
        
   
   
    
     
    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        //return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
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
    public function canShowTab()
    {
        return true;
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
    
    
    protected function _toHtml()
    {
        return parent::_toHtml();
    }
    
    public function getFormHtml()
    {
        
        $html = parent::getFormHtml();
        $html .= $this->getLayout()->createBlock(
            'Mconnect\Csproduct\Block\Adminhtml\Product\Edit\Tab\Customerproduct'
        )->toHtml();
        return $html;
    }
}
