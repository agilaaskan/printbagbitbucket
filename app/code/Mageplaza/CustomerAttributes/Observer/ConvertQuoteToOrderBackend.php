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

namespace Mageplaza\CustomerAttributes\Observer;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\Quote\Model\Quote;
use Magento\Sales\Api\Data\OrderInterface;
use Mageplaza\CustomerAttributes\Helper\Data;
use Zend_Validate_Exception;

/**
 * Class ConvertQuoteToOrderBackend
 * @package Mageplaza\CustomerAttributes\Observer
 */
class ConvertQuoteToOrderBackend extends ConvertQuoteToOrder
{
    /**
     * @param Quote $quote
     * @param OrderInterface $order
     *
     * @return void
     * @throws LocalizedException
     * @throws Zend_Validate_Exception
     * @throws FileSystemException
     * @throws Exception
     */
    public function setCustomData($quote, $order)
    {
        $files = $this->fileUpload->getFiles();

        if (empty($files[$this->scope])) {
            return;
        }

        foreach ($this->formatFilesArray() as $type => $value) {
            foreach ($value as $index => $file) {
                // skip case when no file is chosen
                if (empty($file['tmp_name'])) {
                    continue;
                }

                /** @var Uploader $uploader */
                $uploader = $this->helperData->createObject(Uploader::class, ['fileId' => $file]);

                $directoryRead = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

                $baseTmpMediaPath = ($type === 'account') ? Data::TEMPLATE_CUSTOMER_MEDIA_PATH : $this->helperData->getBaseTmpMediaPath();

                $result = $this->helperData->moveTemporaryFile(
                    $uploader->save($directoryRead->getAbsolutePath(Data::TEMPLATE_CUSTOMER_MEDIA_PATH)),
                    $baseTmpMediaPath
                );

                switch ($type) {
                    case 'shipping_address':
                        $this->addFile($quote, $order, 'shipping_address', $index, $result);
                        break;
                    case 'billing_address':
                        $this->addFile($quote, $order, 'billing_address', $index, $result);
                        if ($quote->getShippingAddress()->getSameAsBilling()) {
                            $this->addFile($quote, $order, 'shipping_address', $index, $result);
                        }
                        break;
                    default:
                        $quote->setData('customer_' . $index, $result);
                        break;
                }
            }
        }
    }

    /**
     * @param Quote $quote
     * @param OrderInterface $order
     * @param string $addressType
     * @param string $attribute
     * @param string $result
     */
    private function addFile($quote, $order, $addressType, $attribute, $result)
    {
        if ($addressType === 'shipping_address') {
            $quote->getShippingAddress()->setData($attribute, $result);
            $order->getShippingAddress()->setData($attribute, $result);
        }

        if ($addressType === 'billing_address') {
            $quote->getBillingAddress()->setData($attribute, $result);
            $order->getBillingAddress()->setData($attribute, $result);
        }
    }

    /**
     * Format files array for multiple uploading files
     *
     * @return array
     * @throws Zend_Validate_Exception
     */
    protected function formatFilesArray()
    {
        $files  = $this->fileUpload->getFiles();
        $result = [];

        foreach ($files[$this->scope] as $key => $value) {
            foreach ($value as $index => $item) {
                if (!is_array($item)) {
                    $item = [$item];
                }

                foreach ($item as $a => $b) {
                    $result[$index][$a][$key] = $b;
                }
            }
        }

        return $result;
    }
}
