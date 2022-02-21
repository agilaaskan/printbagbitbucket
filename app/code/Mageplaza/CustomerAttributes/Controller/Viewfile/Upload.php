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

namespace Mageplaza\CustomerAttributes\Controller\Viewfile;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\CustomerAttributes\Helper\Data;
use Mageplaza\CustomerAttributes\Model\FileResult;

/**
 * Class Upload
 * @package Mageplaza\CustomerAttributes\Controller\Viewfile
 */
class Upload extends Action
{
    /**
     * @var Data
     */
    private $data;

    /**
     * Upload constructor.
     *
     * @param Context $context
     * @param Data $data
     */
    public function __construct(
        Context $context,
        Data $data
    ) {
        $this->data = $data;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $files = $this->data->convertFilesArray();
            if (empty($files)) {
                throw new LocalizedException(__('File is empty.'));
            }

            return $this->data->uploadFile(key($files), $files);
        } catch (Exception $e) {
            $result = [
                'error' => __($e->getMessage())
            ];

            return new FileResult($result);
        }
    }
}
