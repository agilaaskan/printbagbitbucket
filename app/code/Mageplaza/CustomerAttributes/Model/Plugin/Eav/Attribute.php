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

namespace Mageplaza\CustomerAttributes\Model\Plugin\Eav;

use Exception;
use Magento\Customer\Model;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Framework\Exception\LocalizedException;
use Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory;
use Magento\Swatches\Model\Swatch;
use Magento\Swatches\Model\SwatchFactory;
use Mageplaza\CustomerAttributes\Helper\Data;

/**
 * Class Attribute
 * @package Mageplaza\CustomerAttributes\Model\Plugin\Eav
 */
class Attribute
{
    const DEFAULT_STORE_ID = 0;
    /**
     * Base option title used for string operations to detect is option already exists or new
     */
    const BASE_OPTION_TITLE = 'option';

    /**
     * @var CollectionFactory
     */
    protected $swatchCollectionFactory;

    /**
     * @var SwatchFactory
     */
    protected $swatchFactory;

    /**
     * Array which contains links for new created attributes for swatches
     *
     * @var array
     */
    protected $dependencyArray = [];

    /**
     * Swatch existing status
     *
     * @var bool
     */
    protected $isSwatchExists;

    /**
     * @param CollectionFactory $collectionFactory
     * @param SwatchFactory $swatchFactory
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        SwatchFactory $swatchFactory
    ) {
        $this->swatchCollectionFactory = $collectionFactory;
        $this->swatchFactory           = $swatchFactory;
    }

    /**
     * Set base data to Entity\Attribute
     *
     * @param Model\Attribute $attribute
     *
     * @return void
     */
    public function beforeBeforeSave($attribute)
    {
        if ($this->isVisualAttribute($attribute)) {
            $this->setProperOptionsArray($attribute);
        }

        if ($attribute->getData(Swatch::SWATCH_INPUT_TYPE_KEY)) {
            $this->assembleAdditionalData($attribute);
        }
    }

    /**
     * Swatch save operations
     *
     * @param Model\Attribute $attribute
     *
     * @return void
     * @throws Exception
     * @throws LocalizedException
     */
    public function afterAfterSave($attribute)
    {
        if ($this->isVisualAttribute($attribute)) {
            $this->processSwatchOptions($attribute);
            $this->saveDefaultSwatchOptionValue($attribute);
            $this->processVisualAttribute($attribute);
        }
    }

    /**
     * Substitute suitable options and swatches arrays
     *
     * @param Model\Attribute $attribute
     *
     * @return void
     */
    protected function setProperOptionsArray($attribute)
    {
        $defaultValue  = $attribute->getData('defaultvisual');
        $optionsArray  = $attribute->getData('optionvisual');
        $swatchesArray = $attribute->getData('swatchvisual');
        if (!empty($optionsArray)) {
            $attribute->setData('option', $optionsArray);
        }
        if (!empty($defaultValue)) {
            $attribute->setData('default', $defaultValue);
        } else {
            $attribute->setData('default', [0 => $attribute->getDefaultValue()]);
        }
        if (!empty($swatchesArray)) {
            $attribute->setData('swatch', $swatchesArray);
        }
    }

    /**
     * Creates array which link new option ids
     *
     * @param Model\Attribute $attribute
     *
     * @return Model\Attribute
     * @throws LocalizedException
     */
    protected function processSwatchOptions($attribute)
    {
        $optionsArray = $attribute->getData('option');

        if (!empty($optionsArray) && is_array($optionsArray)) {
            $optionsArray = $this->prepareOptionIds($optionsArray);

            /**
             * Because default source model set \Mageplaza\CustomerAttributes\Model\Entity\Attribute\Source\Table in
             * save controller is overwrite by resource model attribute before save with new object
             * Check \Magento\Eav\Model\ResourceModel\Entity\Attribute::_beforeSave
             *
             * @var Table $source
             */
            $source = $attribute->getSource();

            $attributeSavedOptions = $source->getAllOptions(false);
            $this->prepareOptionLinks($optionsArray, $attributeSavedOptions);
        }

        return $attribute;
    }

    /**
     * Get options array without deleted items
     *
     * @param array $optionsArray
     *
     * @return array
     */
    protected function prepareOptionIds(array $optionsArray)
    {
        if (isset($optionsArray['value']) && is_array($optionsArray['value'])) {
            foreach (array_keys($optionsArray['value']) as $optionId) {
                if (isset($optionsArray['delete']) && isset($optionsArray['delete'][$optionId])
                    && $optionsArray['delete'][$optionId] == 1
                ) {
                    unset($optionsArray['value'][$optionId]);
                }
            }
        }

        return $optionsArray;
    }

    /**
     * Create links for non existed swatch options
     *
     * @param array $optionsArray
     * @param array $attributeSavedOptions
     *
     * @return void
     */
    protected function prepareOptionLinks(array $optionsArray, array $attributeSavedOptions)
    {
        $dependencyArray = [];
        if (is_array($optionsArray['value'])) {
            $optionCounter = 0;
            foreach (array_keys($optionsArray['value']) as $baseOptionId) {
                if (empty($attributeSavedOptions[$optionCounter]['value'])) {
                    continue;
                }
                $dependencyArray[$baseOptionId] = $attributeSavedOptions[$optionCounter]['value'];
                $optionCounter++;
            }
        }

        $this->dependencyArray = $dependencyArray;
    }

    /**
     * @param Model\Attribute $attribute
     *
     * @return void
     * @throws Exception
     */
    protected function processVisualAttribute($attribute)
    {
        $swatchArray = $attribute->getData('swatch/value');
        if (isset($swatchArray) && is_array($swatchArray)) {
            foreach ($swatchArray as $optionId => $value) {
                $optionId          = $this->getAttributeOptionId($optionId);
                $isOptionForDelete = $this->isOptionForDelete($attribute, $optionId);
                if ($optionId === null || $isOptionForDelete) {
                    //option was deleted by button with basket
                    continue;
                }
                $swatch = $this->loadSwatchIfExists($optionId, self::DEFAULT_STORE_ID);

                $swatchType = $this->determineSwatchType($value);

                $this->saveSwatchData($swatch, $optionId, self::DEFAULT_STORE_ID, $swatchType, $value);
                $this->isSwatchExists = null;
            }
        }
    }

    /**
     * @param string $value
     *
     * @return int
     */
    protected function determineSwatchType($value)
    {
        $swatchType = Swatch::SWATCH_TYPE_EMPTY;
        if (!empty($value) && $value[0] == '#') {
            $swatchType = Swatch::SWATCH_TYPE_VISUAL_COLOR;
        } else {
            if (!empty($value) && $value[0] == '/') {
                $swatchType = Swatch::SWATCH_TYPE_VISUAL_IMAGE;
            }
        }

        return $swatchType;
    }

    /**
     * Get option id. If it not exist get it from dependency link array
     *
     * @param integer $optionId
     *
     * @return int
     */
    protected function getAttributeOptionId($optionId)
    {
        if (substr($optionId, 0, 6) == self::BASE_OPTION_TITLE) {
            $optionId = isset($this->dependencyArray[$optionId]) ? $this->dependencyArray[$optionId] : null;
        }

        return $optionId;
    }

    /**
     * Check if is option for delete
     *
     * @param Model\Attribute $attribute
     * @param integer $optionId
     *
     * @return bool
     */
    protected function isOptionForDelete($attribute, $optionId)
    {
        $isOptionForDelete = $attribute->getData('option/delete/' . $optionId);

        return isset($isOptionForDelete) && $isOptionForDelete;
    }

    /**
     * Load swatch if it exists in database
     *
     * @param int $optionId
     * @param int $storeId
     *
     * @return Swatch
     */
    protected function loadSwatchIfExists($optionId, $storeId)
    {
        $collection = $this->swatchCollectionFactory->create();
        $collection->addFieldToFilter('option_id', $optionId);
        $collection->addFieldToFilter('store_id', $storeId);
        $collection->setPageSize(1);

        /** @var Swatch $loadedSwatch */
        $loadedSwatch = $collection->getFirstItem();
        if ($loadedSwatch->getId()) {
            $this->isSwatchExists = true;
        }

        return $loadedSwatch;
    }

    /**
     * Save operation
     *
     * @param Swatch $swatch
     * @param integer $optionId
     * @param integer $storeId
     * @param integer $type
     * @param string $value
     *
     * @return void
     * @throws Exception
     */
    protected function saveSwatchData($swatch, $optionId, $storeId, $type, $value)
    {
        if ($this->isSwatchExists) {
            $swatch->setData('type', $type);
            $swatch->setData('value', $value);
        } else {
            $swatch->setData('option_id', $optionId);
            $swatch->setData('store_id', $storeId);
            $swatch->setData('type', $type);
            $swatch->setData('value', $value);
        }
        $swatch->save();
    }

    /**
     * Save default swatch value using Swatch model instead of Eav model
     *
     * @param Model\Attribute $attribute
     *
     * @return void
     */
    protected function saveDefaultSwatchOptionValue($attribute)
    {
        $defaultValue = $attribute->getData('default');
        if (!empty($defaultValue)) {
            $defaultValue = implode(',', $defaultValue);
            /** @var Swatch $swatch */
            $swatch = $this->swatchFactory->create();
            // created and removed on frontend option not exists in dependency array
            if (substr($defaultValue, 0, 6) == self::BASE_OPTION_TITLE &&
                isset($this->dependencyArray[$defaultValue])
            ) {
                $defaultValue = $this->dependencyArray[$defaultValue];
            }
            $swatch->getResource()->saveDefaultSwatchOption($attribute->getId(), $defaultValue);
        }
    }

    /**
     * @param Model\Attribute $attribute
     * @param bool $result
     *
     * @return bool
     */
    public function afterUsesSource($attribute, $result)
    {
        if ($this->isVisualAttribute($attribute)) {
            return true;
        }

        return $result;
    }

    /**
     * @param Model\Attribute $attribute
     *
     * @return bool
     */
    public function isVisualAttribute($attribute)
    {
        if (!$attribute->hasData(Swatch::SWATCH_INPUT_TYPE_KEY)) {
            $serializedAdditionalData = $attribute->getData('additional_data');
            if ($serializedAdditionalData) {
                $additionalData = Data::jsonDecode($serializedAdditionalData);
                if (isset($additionalData[Swatch::SWATCH_INPUT_TYPE_KEY])) {
                    $attribute->setData(Swatch::SWATCH_INPUT_TYPE_KEY, $additionalData[Swatch::SWATCH_INPUT_TYPE_KEY]);
                }
            }
        }

        $isSelectType = in_array($attribute->getFrontendInput(), ['select', 'multiselect']);
        $isVisual     = $attribute->getData(Swatch::SWATCH_INPUT_TYPE_KEY) === Swatch::SWATCH_INPUT_TYPE_VISUAL;

        return $isSelectType && $isVisual;
    }

    /**
     * @param Model\Attribute $attribute
     *
     * @return void
     */
    public function assembleAdditionalData($attribute)
    {
        $initialAdditionalData = $this->getAdditionalData($attribute);

        $dataToAdd[Swatch::SWATCH_INPUT_TYPE_KEY] = $attribute->getData(Swatch::SWATCH_INPUT_TYPE_KEY);

        $additionalData = array_merge($initialAdditionalData, $dataToAdd);
        $attribute->setData('additional_data', Data::jsonEncode($additionalData));
    }

    /**
     * @param Model\Attribute $attribute
     *
     * @return array
     */
    public function getAdditionalData($attribute)
    {
        $additionalData = (string) $attribute->getData('additional_data');
        if (!empty($additionalData)) {
            return Data::jsonDecode($additionalData);
        }

        return [];
    }
}
