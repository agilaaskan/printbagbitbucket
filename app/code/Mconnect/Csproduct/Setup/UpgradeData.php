<?php
namespace Mconnect\Csproduct\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute\Set as AttributeSet;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CustomerSetupFactory
     */
    protected $customerSetupFactory;
    
    /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;
    
    /**
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }
    

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        if (version_compare($context->getVersion(), '2.0.5', '<')) {
            $customerSetup1 = $this->customerSetupFactory->create(['setup' => $setup]);
        
            $customerEntity1 = $customerSetup1->getEavConfig()->getEntityType('customer');
            $attributeSetId1 = $customerEntity1->getDefaultAttributeSetId();
        
            $customerSetup1->removeAttribute($attributeSetId1, 'followproductsfromgroup');
            
            
        
        /** @var CustomerSetup $customerSetup */
            $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        
            $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
            $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        
        /** @var $attributeSet AttributeSet */
            $attributeSet = $this->attributeSetFactory->create();
            $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        
            $customerSetup->addAttribute(
                Customer::ENTITY,
                'followproductsfromgroup',
                [
                'type' => 'int',
                'label' => 'Follow Products From Group',
                'input' => 'select',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                'required' => 0,
                'default' => '1',
                'sort_order' => 100,
                'system' => false,
                'position' => 100,
                'global' => 1,
                'visible' => 0,
                'user_defined' => 1,
                'visible_on_front' => 1,
                'adminhtml_only' => '1',
                ]
            );
        
            $attribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'followproductsfromgroup')
            ->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
            'used_in_forms' => ['adminhtml_customer'],
            ]);
        
            $attribute->save();
        }
    }
}
