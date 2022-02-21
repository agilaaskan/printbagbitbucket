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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Filesystem;
use Magento\Framework\Url\DecoderInterface;
use Magento\MediaStorage\Helper\File\Storage;

/**
 * Class Index
 * @package Mageplaza\CustomerAttributes\Controller\Viewfile
 */
class Index extends Action
{
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var DecoderInterface
     */
    protected $urlDecoder;

    /**
     * @var FileFactory
     */
    protected $fileFactory;

    /**
     * Index constructor.
     *
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param DecoderInterface $urlDecoder
     * @param FileFactory $fileFactory
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        DecoderInterface $urlDecoder,
        FileFactory $fileFactory
    ) {
        $this->fileFactory      = $fileFactory;
        $this->urlDecoder       = $urlDecoder;
        $this->resultRawFactory = $resultRawFactory;

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Raw|ResultInterface|null
     * @throws NotFoundException
     * @throws FileSystemException
     * @throws Exception
     */
    public function execute()
    {
        [$file, $plain] = $this->getFileParams();

        /** @var Filesystem $filesystem */
        $filesystem = $this->_objectManager->get(Filesystem::class);
        $directory  = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $fileName   = $this->getRequest()->getParam('type') . '/' . ltrim($file, '/');
        $path       = $directory->getAbsolutePath($fileName);
        if (mb_strpos($path, '..') !== false
            || (!$directory->isFile($fileName)
                && !$this->_objectManager->get(
                    Storage::class
                )->processStorageFile($path))
        ) {
            throw new NotFoundException(__('Page not found.'));
        }

        if ($plain) {
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            switch (strtolower($extension)) {
                case 'gif':
                    $contentType = 'image/gif';
                    break;
                case 'jpg':
                case 'jpeg':
                    $contentType = 'image/jpeg';
                    break;
                case 'png':
                    $contentType = 'image/png';
                    break;
                default:
                    $contentType = 'application/octet-stream';
                    break;
            }
            $stat          = $directory->stat($fileName);
            $contentLength = $stat['size'];
            $contentModify = $stat['mtime'];

            /** @var Raw $resultRaw */
            $resultRaw = $this->resultRawFactory->create();
            $resultRaw->setHttpResponseCode(200)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-type', $contentType, true)
                ->setHeader('Content-Length', $contentLength)
                ->setHeader('Last-Modified', date('r', $contentModify));
            $resultRaw->setContents($directory->readFile($fileName));

            return $resultRaw;
        }

        $name = pathinfo($path, PATHINFO_BASENAME);
        $this->fileFactory->create(
            $name,
            ['type' => 'filename', 'value' => $fileName],
            DirectoryList::MEDIA
        );

        return null;
    }

    /**
     * Get parameters from request.
     *
     * @return array
     * @throws NotFoundException
     */
    private function getFileParams()
    {
        if ($this->getRequest()->getParam('file')) {
            // download file
            $file = $this->urlDecoder->decode($this->getRequest()->getParam('file'));

            return [$file, false];
        }

        if ($this->getRequest()->getParam('image')) {
            // show plain image
            $file = $this->urlDecoder->decode($this->getRequest()->getParam('image'));

            return [$file, true];
        }

        throw new NotFoundException(__('Page not found.'));
    }
}
