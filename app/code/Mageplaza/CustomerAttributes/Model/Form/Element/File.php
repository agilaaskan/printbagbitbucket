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

namespace Mageplaza\CustomerAttributes\Model\Form\Element;

use Magento\Backend\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Mageplaza\CustomerAttributes\Helper\Data as CustomerAttributesHelper;

/**
 * Class File
 * @package Mageplaza\CustomerAttributes\Model\Form\Element
 */
class File extends \Magento\Customer\Block\Adminhtml\Form\Element\File
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var FormKey
     */
    private $formKey;

    /**
     * File constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param Data $adminhtmlData
     * @param Repository $assetRepo
     * @param EncoderInterface $urlEncoder
     * @param array $data
     * @param RequestInterface $request
     * @param UrlInterface $urlBuilder
     * @param FormKey $formKey
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        Data $adminhtmlData,
        Repository $assetRepo,
        EncoderInterface $urlEncoder,
        $data = [],
        RequestInterface $request,
        UrlInterface $urlBuilder,
        FormKey $formKey
    ) {
        $this->request    = $request;
        $this->urlBuilder = $urlBuilder;
        $this->formKey    = $formKey;

        parent::__construct(
            $factoryElement,
            $factoryCollection,
            $escaper,
            $adminhtmlData,
            $assetRepo,
            $urlEncoder,
            $data
        );
    }

    /**
     * Return Image URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        if (strpos($this->_adminhtmlData->getPageHelpUrl(), 'Magento_Sales/order/address') !== false) {
            $type = 'customer_address';
        } else {
            $type = 'customer';
        }

        return $this->_adminhtmlData->getUrl(
            'customer/viewfile/index',
            ['file' => $this->urlEncoder->encode($this->getValue()), 'type' => $type]
        );
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getElementHtml()
    {
        if (!$this->request->getFullActionName() === 'sales_order_create_index') {
            return parent::getElementHtml();
        }

        return parent::getElementHtml() . $this->addValueToHiddenInputFileJs();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    private function addValueToHiddenInputFileJs()
    {
        if (strpos($this->getName(), 'order[account]') !== false) {
            $ajaxUrlType   = 'customer_upload';
            $inputFileId   = 'file_upload_' . $this->getHtmlId();
            $fileAttribute = 'customer[' . $this->getId() . ']';
        } else {
            $ajaxUrlType   = 'address_upload';
            $inputFileId   = 'file_upload_address_' . $this->getHtmlId();
            $fileAttribute = $this->getId();
        }

        $ajaxUrl = $this->urlBuilder->getUrl(
            'customer/file/' . $ajaxUrlType,
            ['form_key' => $this->formKey->getFormKey()]
        );

        $config = CustomerAttributesHelper::jsonEncode([
            'ajaxUrl'       => $ajaxUrl,
            'inputFileId'   => $inputFileId,
            'fileAttribute' => $fileAttribute,
            'htmlId'        => $this->getHtmlId()
        ]);

        return '<script type="text/javascript">
            require([\'jquery\', \'Mageplaza_CustomerAttributes/js/form/order-file-upload\'], function ($, mpCustomerAttributesUpload) {
                mpCustomerAttributesUpload(' . $config . ';)
            });
        </script>';
    }
}
