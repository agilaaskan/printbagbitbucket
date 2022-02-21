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

namespace Mageplaza\CustomerAttributes\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Customer\Api\CustomerMetadataInterface;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Model\Metadata\ElementFactory;
use Magento\Eav\Model\AttributeDataFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Url\EncoderInterface;
use Magento\Sales\Block\Adminhtml\Order\AbstractOrder;
use Magento\Sales\Helper\Admin;

/**
 * Class ExtraCustomerInfo
 * @package Mageplaza\CustomerAttributes\Block\Adminhtml
 */
class ExtraCustomerInfo extends AbstractOrder
{
    /**
     * @var CustomerMetadataInterface
     */
    private $metadata;

    /**
     * @var ElementFactory
     */
    private $elementFactory;

    /**
     * @var EncoderInterface
     */
    private $urlEncoder;

    /**
     * ExtraCustomerInfo constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Admin $adminHelper
     * @param CustomerMetadataInterface $metadata
     * @param ElementFactory $elementFactory
     * @param EncoderInterface $urlEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Admin $adminHelper,
        CustomerMetadataInterface $metadata,
        ElementFactory $elementFactory,
        EncoderInterface $urlEncoder,
        array $data = []
    ) {
        $this->metadata       = $metadata;
        $this->elementFactory = $elementFactory;
        $this->urlEncoder     = $urlEncoder;

        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Return array of additional account data
     * Value is option style array
     *
     * @return array
     * @throws LocalizedException
     */
    public function getMediaFieldData()
    {
        $accountData = [];
        $entityType  = 'customer';

        /* @var AttributeMetadataInterface $attribute */
        foreach ($this->metadata->getAllAttributesMetadata($entityType) as $attribute) {
            if (!$attribute->isVisible()
                || $attribute->isSystem()
                || !in_array($attribute->getFrontendInput(), ['image', 'file'], true)) {
                continue;
            }
            $orderKey   = sprintf('customer_%s', $attribute->getAttributeCode());
            $orderValue = $this->getOrder()->getData($orderKey);
            if ($orderValue) {
                $metadataElement         = $this->elementFactory->create($attribute, $orderValue, $entityType);
                $value                   = $metadataElement->outputValue(AttributeDataFactory::OUTPUT_FORMAT_HTML);
                $sortOrder               = ($attribute->getSortOrder() + $attribute->isUserDefined()) ? 200 : 0;
                $sortOrder               = $this->_prepareAccountDataSortOrder($accountData, $sortOrder);
                $accountData[$sortOrder] = [
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->getAttributeHtml($value, $attribute->getFrontendInput()),
                ];
            }
        }
        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }

    /**
     * Find sort order for account data
     * Sort Order used as array key
     *
     * @param array $data
     * @param int $sortOrder
     *
     * @return int
     */
    protected function _prepareAccountDataSortOrder(array $data, $sortOrder)
    {
        if (isset($data[$sortOrder])) {
            return $this->_prepareAccountDataSortOrder($data, $sortOrder + 1);
        }

        return $sortOrder;
    }

    /**
     * @param string $value
     * @param string $input
     *
     * @return string string
     */
    protected function getAttributeHtml($value, $input)
    {
        switch ($input) {
            case 'image':
                $url = $this->getUrl(
                    'customer/viewfile/index',
                    ['image' => $this->urlEncoder->encode($value), 'type' => 'customer']
                );

                return '<a href="' . $url . '" target="_blank">
                <img src="' . $url . '" alt="image" style="max-height: 50px;"/></a>';
            case 'file':
                $url = $this->getUrl(
                    'customer/viewfile/index',
                    ['file' => $this->urlEncoder->encode($value), 'type' => 'customer']
                );

                return '<a href="' . $url . '" target="_blank">Download</a>';
            default:
                return '';
        }
    }
}
