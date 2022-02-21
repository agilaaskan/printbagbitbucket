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

use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Element\Template;
use Magento\Swatches\Helper\Media;
use Magento\Swatches\Model\ResourceModel\Swatch\CollectionFactory;
use Mageplaza\CustomerAttributes\Helper\Data;

/**
 * Class TextareaVisual
 * @package Mageplaza\CustomerAttributes\Block\Form\Renderer
 */
class TextareaVisual extends Text
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * TextareaVisual constructor.
     *
     * @param Template\Context $context
     * @param Media $swatchHelper
     * @param CollectionFactory $swatchCollection
     * @param EncoderInterface $urlEncoder
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Media $swatchHelper,
        CollectionFactory $swatchCollection,
        EncoderInterface $urlEncoder,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $swatchHelper, $swatchCollection, $urlEncoder, $data);
    }

    /**
     * @return bool|string
     */
    public function getTinymceConfig()
    {
        return $this->helper->getTinymceConfig();
    }
}
