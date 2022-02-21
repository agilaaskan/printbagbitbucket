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

namespace Mageplaza\CustomerAttributes\Block\Form\Renderer;

use Magento\Eav\Model\Attribute;
use Magento\Eav\Model\Entity\Type;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory;

/**
 * Class AbstractRenderer
 * @package Mageplaza\CustomerAttributes\Block\Form\Renderer
 */
abstract class AbstractRenderer extends Template
{
    /**
     * @var Media
     */
    protected $swatchHelper;

    /**
     * @var CollectionFactory
     */
    protected $swatchCollection;

    /**
     * @var EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @var Type
     */
    protected $_entityType;

    /**
     * AbstractRenderer constructor.
     *
     * @param Template\Context $context
     * @param Media $swatchHelper
     * @param CollectionFactory $swatchCollection
     * @param EncoderInterface $urlEncoder
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Media $swatchHelper,
        CollectionFactory $swatchCollection,
        EncoderInterface $urlEncoder,
        array $data = []
    ) {
        $this->swatchHelper     = $swatchHelper;
        $this->swatchCollection = $swatchCollection;
        $this->urlEncoder       = $urlEncoder;

        parent::__construct($context, $data);
    }

    /**
     * @var Attribute
     */
    protected $_attribute;

    /**
     * @var AbstractModel
     */
    protected $_entity;

    /**
     * @var string
     */
    protected $_fieldIdFormat = '%1$s';

    /**
     * @var string
     */
    protected $_fieldNameFormat = '%1$s';

    /**
     * @param Attribute $attribute
     *
     * @return $this
     */
    public function setAttributeObject(Attribute $attribute)
    {
        $this->_attribute = $attribute;

        return $this;
    }

    /**
     * @return Attribute
     */
    public function getAttributeObject()
    {
        return $this->_attribute;
    }

    /**
     * @param AbstractModel $entity
     *
     * @return $this
     */
    public function setEntity(AbstractModel $entity)
    {
        $this->_entity = $entity;

        return $this;
    }

    /**
     * @return AbstractModel
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Return frontend class by attribute validate rules
     *
     * @return string|false
     */
    protected function getFrontendClass()
    {
        $class         = false;
        $validateRules = $this->getAttributeObject()->getValidateRules();
        if (!empty($validateRules['input_validation'])) {
            switch ($validateRules['input_validation']) {
                case 'alphanumeric':
                    $class = 'validate-alphanum';
                    break;
                case 'numeric':
                    $class = 'validate-digits';
                    break;
                case 'alpha':
                    $class = 'validate-alpha';
                    break;
                case 'email':
                    $class = 'validate-email';
                    break;
                case 'url':
                    $class = 'validate-url';
                    break;
                case 'date':
                    $class = 'product-custom-option datetime-picker input-text validate-date';
                    break;
            }
        }

        return $class;
    }

    /**
     *
     * @return string
     */
    public function getValue()
    {
        $value = $this->getEntity()->getData($this->getAttributeObject()->getAttributeCode());

        if ($value === null) {
            $value = $this->getAttributeObject()->getDefaultValue();
        }

        return $value;
    }

    /**
     * @param string|null $index
     *
     * @return string
     */
    public function getHtmlId($index = null)
    {
        $format = $this->_fieldIdFormat;
        if ($index !== null) {
            $format .= '[%2$s]';
        }

        return sprintf($format, $this->getAttributeObject()->getAttributeCode(), $index);
    }

    /**
     *
     * @return string
     */
    public function getHtmlClass()
    {
        $classes = [];
        if ($this->isRequired()) {
            $classes[] = 'required-entry';
        }
        if ($frontendClass = $this->getFrontendClass()) {
            $classes[] = $frontendClass;
        }

        return empty($classes) ? '' : ' ' . implode(' ', $classes);
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->getAttributeObject()->getIsRequired();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getAttributeObject()->getStoreLabel();
    }

    /**
     * Set format for HTML element(s) id attribute
     *
     * @param string $format
     *
     * @return $this
     */
    public function setFieldIdFormat($format)
    {
        $this->_fieldIdFormat = $format;

        return $this;
    }

    /**
     * Set format for HTML element(s) name attribute
     *
     * @param string $format
     *
     * @return $this
     */
    public function setFieldNameFormat($format)
    {
        $this->_fieldNameFormat = $format;

        return $this;
    }

    /**
     * @param Type $entityType
     *
     * @return $this
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = $entityType;

        return $this;
    }
}
