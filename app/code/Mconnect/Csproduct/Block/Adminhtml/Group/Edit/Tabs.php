<?php
/**
 * M-Connect Solutions.
 *
 * NOTICE OF LICENSE
 *

 *
 * @category   Catalog
 * @package   Mconnect_Csproduct
 * @author      M-Connect Solutions (http://www.magentoconnect.us)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mconnect\Csproduct\Block\Adminhtml\Group\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
        
    protected function _construct()
    {
        parent::_construct();
        $this->setId('groupcustomerspecificproduct_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Group Information'));
    }
    
    
    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $_helper = $objectManager->get('Mconnect\Csproduct\Helper\Data');
        
        
        
        $multiple_store = 1;
        
        $this->addTab(
            'form_section',
            [
                'label' => __('Group Information'),
                'title' => __('Group Information'),
                'active' => true
            ]
        );
        
        
        if ($this->getRequest()->getParam('id') && ($_helper->getWebsiteIdByGroupId($this->getRequest()->getParam('id')))) {
            $this->addTab(
                'grid_section',
                [
                    'label' => __('Customer Group Specific Products'),
                    'title' => __('Customer Group Specific Products'),
                    'url' => $this->getUrl('csproduct/group/groupproduct', ['_current' => true]),
                    'class' => 'ajax'
                ]
            );
            
            /*
			$this->addTab(
				'categories_section',
				[
					'label' => __('Specific Categories'),
					'title' => __('Specific Categories'),
					'url' => $this->getUrl('csproduct/group/specificcategories', ['_current' => true]),
					'class' => 'ajax'
				]
			);*/
        }
        return parent::_beforeToHtml();
    }
}
