<?php
namespace Mconnect\Csproduct\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Field;

class Customergrouptab extends AbstractModifier
{

    // Components indexes
    const CUSTOM_FIELDSET_INDEX = 'custom_fieldset';
    const CUSTOM_FIELDSET_CONTENT = 'custom_fieldset_content';
    const CONTAINER_HEADER_NAME = 'custom_fieldset_content_header';

    // Fields names
    const FIELD_NAME_TEXT = 'example_text_field';
    const FIELD_NAME_SELECT = 'example_select_field';
    const CS_GROUP_MULTISELECT = 'cs_group_multiselect';

    /**
     * @var \Magento\Catalog\Model\Locator\LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var array
     */
    protected $meta = [];
    
    protected $_objectManager = null;
    
    protected $csgroupproduct;
    

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Mconnect\Csproduct\Model\ResourceModel\Csgroupproduct\CollectionFactory $csgroupproduct,
        \Mconnect\Csproduct\Helper\Data $helper,
        UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->_objectManager = $objectManager;
        $this->csgroupproduct = $csgroupproduct;
        $this->_helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Data modifier, does nothing in our example.
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Meta-data modifier: adds ours fieldset
     *
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $multiple_store = 1;
        if ($multiple_store) {
            return $meta;
        }
        $this->meta = $meta;
        $this->addCustomFieldset();
        return $this->meta;
    }

    /**
     * Merge existing meta-data with our meta-data (do not overwrite it!)
     *
     * @return void
     */
    protected function addCustomFieldset()
    {
        $this->meta = array_merge_recursive(
            $this->meta,
            [
                static::CUSTOM_FIELDSET_INDEX => $this->getFieldsetConfig(),
            ]
        );
    }

    /**
     * Declare ours fieldset config
     *
     * @return array
     */
    protected function getFieldsetConfig()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Specific Customer Group'),
                        'componentType' => Fieldset::NAME,
                        'dataScope' => static::DATA_SCOPE_PRODUCT, // save data in the product data
                        'provider' => static::DATA_SCOPE_PRODUCT . '_data_source',
                        'ns' => static::FORM_NAME,
                        'collapsible' => true,
                        'sortOrder' => 201,
                        'opened' => false,
                    ],
                ],
            ],
            'children' => [
              //  static::CONTAINER_HEADER_NAME => $this->getHeaderContainerConfig(10),
               // static::FIELD_NAME_TEXT => $this->getTextFieldConfig(20),
               // static::FIELD_NAME_SELECT => $this->getSelectFieldConfig(30),
                static::CS_GROUP_MULTISELECT => $this->getMultiSelectFieldConfig(40),
            ],
        ];
    }

    /**
     * Get config for header container
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getHeaderContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => 'ui/form/components/complex',
                        'sortOrder' => $sortOrder,
                        'content' => __('Select Customer Group'),
                    ],
                ],
            ],
            'children' => [],
        ];
    }

    
    

    /**
     * Example multi-select field config
     *
     * @param $sortOrder
     * @return array
     */
    protected function getMultiSelectFieldConfig($sortOrder)
    {
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        
        
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Select Customer Group'),
                        'componentType' => Field::NAME,
                        'formElement' => MultiSelect::NAME,
                        'dataScope' => static::CS_GROUP_MULTISELECT,
                        'dataType' => Text::NAME,
                        'sortOrder' => $sortOrder,
                        'options' => $this->_getOptions(),
                        'visible' => true,
                        'disabled' => false,
                        'value'   => $this->_getGroupSelectedValues($productId)
                    ],
                ],
            ],
        ];
    }

    /**
     * Get example options as an option array:
     *      [
     *          label => string,
     *          value => option_id
     *      ]
     *
     * @return array
     */
    protected function _getOptions()
    {
        
        
        $getGroupCollection=$this->_objectManager->get('\Magento\Customer\Model\ResourceModel\Group\Collection');
        return  $getGroupCollection->toOptionArray();
    }
    
    protected function _getGroupSelectedValues($productId)
    {
        //return $productId;
        
        
        $csgCollection = $this->csgroupproduct->create();
        $csgCollection->addFieldToFilter('product_id', $productId);
        $availableGroup = $csgCollection->getColumnValues('group_id');
        return $str = implode(",", $availableGroup);
    }
}
