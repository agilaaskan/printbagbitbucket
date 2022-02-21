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

use Magento\Framework\Data\Form\FilterFactory;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Element\Html;
use Magento\Framework\View\Element\Template;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory;

/**
 * Class Date
 * @package Mageplaza\CustomerAttributes\Block\Form\Renderer
 */
class Date extends AbstractRenderer
{
    /**
     * @var Html\Date
     */
    protected $dateElement;

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * Date constructor.
     *
     * @param Template\Context $context
     * @param Media $swatchHelper
     * @param CollectionFactory $swatchCollection
     * @param EncoderInterface $urlEncoder
     * @param Html\Date $dateElement
     * @param FilterFactory $filterFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Media $swatchHelper,
        CollectionFactory $swatchCollection,
        EncoderInterface $urlEncoder,
        Html\Date $dateElement,
        FilterFactory $filterFactory,
        array $data = []
    ) {
        $this->_isScopePrivate = true;
        $this->dateElement     = $dateElement;
        $this->filterFactory   = $filterFactory;

        parent::__construct($context, $swatchHelper, $swatchCollection, $urlEncoder, $data);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        $value = parent::getValue();

        return $value ? $this->applyOutputFilter($value) : '';
    }

    /**
     * Apply output filter to value
     *
     * @param string $value
     *
     * @return string
     */
    protected function applyOutputFilter($value)
    {
        $value  = date("Y-m-d H:i:s", strtotime($value));
        $filter = $this->filterFactory->create('date', ['format' => $this->getDateFormat()]);
        if ($filter) {
            $value = $filter->outputFilter($value);
        }

        return $value;
    }

    /**
     * Create correct date field
     *
     * @return string
     */
    public function getFieldHtml()
    {
        $this->dateElement->setData([
            'name'         => $this->getHtmlId(),
            'id'           => $this->getHtmlId(),
            'class'        => $this->getHtmlClass(),
            'value'        => $this->getValue(),
            'date_format'  => $this->getDateFormat(),
            'image'        => $this->getViewFileUrl('Magento_Theme::calendar.png'),
            'change_month' => 'true',
            'change_year'  => 'true',
            'show_on'      => 'both'
        ]);

        return $this->dateElement->getHtml();
    }

    /**
     * Returns format which will be applied for date field in javascript
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->_localeDate->getDateFormatWithLongYear();
    }
}
