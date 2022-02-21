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

namespace Mageplaza\CustomerAttributes\Ui\Component;

use Magento\Customer\Model\Attribute;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class ColumnFactory
 * @package Mageplaza\CustomerAttributes\Ui\Component
 */
class ColumnFactory
{
    /**
     * @var UiComponentFactory
     */
    protected $componentFactory;

    /**
     * @var array
     */
    protected $jsComponentMap = [
        'text'        => 'Magento_Ui/js/grid/columns/column',
        'select'      => 'Magento_Ui/js/grid/columns/select',
        'multiselect' => 'Magento_Ui/js/grid/columns/select',
        'date'        => 'Magento_Ui/js/grid/columns/date',
    ];

    /**
     * @var array
     */
    protected $dataTypeMap = [
        'default'            => 'text',
        'text'               => 'text',
        'boolean'            => 'select',
        'select'             => 'select',
        'select_visual'      => 'select',
        'multiselect'        => 'multiselect',
        'multiselect_visual' => 'multiselect',
        'date'               => 'date',
    ];

    /**
     * @param UiComponentFactory $componentFactory
     */
    public function __construct(UiComponentFactory $componentFactory)
    {
        $this->componentFactory = $componentFactory;
    }

    /**
     * @param Attribute $attribute
     * @param ContextInterface $context
     * @param array $config
     *
     * @return UiComponentInterface
     * @throws LocalizedException
     */
    public function create($attribute, $context, array $config = [])
    {
        $columnName = $attribute->getAttributeCode();
        $config     = array_merge([
            'label'     => __($attribute->getDefaultFrontendLabel()),
            'dataType'  => $this->getDataType($attribute->getFrontendInput()),
            'component' => $this->getJsComponent($this->getDataType($attribute->getFrontendInput())),
            'filter'    => ($attribute->getIsFilterableInGrid())
                ? $this->getFilterType($attribute->getFrontendInput())
                : null,
        ], $config);

        if ($attribute->usesSource() || strpos($attribute->getFrontendInput(), 'select') !== false) {
            $config['options'] = $attribute->getSource()->getAllOptions();
        }

        $arguments = [
            'data'    => [
                'config' => $config,
            ],
            'context' => $context,
        ];

        return $this->componentFactory->create($columnName, 'column', $arguments);
    }

    /**
     * @param string $dataType
     *
     * @return string
     */
    protected function getJsComponent($dataType)
    {
        return $this->jsComponentMap[$dataType];
    }

    /**
     * @param string $frontendType
     *
     * @return string
     */
    protected function getDataType($frontendType)
    {
        return isset($this->dataTypeMap[$frontendType])
            ? $this->dataTypeMap[$frontendType]
            : $this->dataTypeMap['default'];
    }

    /**
     * Retrieve filter type by $frontendInput
     *
     * @param string $frontendInput
     *
     * @return string
     */
    protected function getFilterType($frontendInput)
    {
        $filtersMap = ['date' => 'dateRange'];
        $result     = array_replace_recursive($this->dataTypeMap, $filtersMap);

        return isset($result[$frontendInput]) ? $result[$frontendInput] : $result['default'];
    }
}
