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

namespace Mageplaza\CustomerAttributes\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Customer\Model\AttributeFactory;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\Entity\Type;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mageplaza\CustomerAttributes\Helper\Data;

/**
 * Class Attribute
 * @package Mageplaza\CustomerAttributes\Controller\Adminhtml
 */
abstract class Attribute extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Mageplaza_CustomerAttributes::customer_attributes';

    protected $type = 'customer';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Customer Address Entity Type instance
     *
     * @var Type
     */
    protected $_entityType;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Config
     */
    protected $_eavConfig;

    /**
     * @var SetFactory
     */
    protected $attrSetFactory;

    /**
     * @var AttributeFactory
     */
    protected $_attrFactory;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     * @param Config $eavConfig
     * @param SetFactory $attrSetFactory
     * @param AttributeFactory $attrFactory
     * @param FilterManager $filterManager
     * @param Data $dataHelper
     * @param IndexerRegistry $indexerRegistry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        Config $eavConfig,
        SetFactory $attrSetFactory,
        AttributeFactory $attrFactory,
        FilterManager $filterManager,
        Data $dataHelper,
        IndexerRegistry $indexerRegistry
    ) {
        $this->_coreRegistry     = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        $this->_eavConfig        = $eavConfig;
        $this->attrSetFactory    = $attrSetFactory;
        $this->_attrFactory      = $attrFactory;
        $this->filterManager     = $filterManager;
        $this->dataHelper        = $dataHelper;
        $this->indexerRegistry   = $indexerRegistry;

        parent::__construct($context);
    }

    /**
     * Return Customer Address Entity Type instance
     *
     * @return Type
     * @throws LocalizedException
     */
    protected function _getEntityType()
    {
        if ($this->_entityType === null) {
            $this->_entityType = $this->_eavConfig->getEntityType($this->type);
        }

        return $this->_entityType;
    }

    /**
     * Load layout, set breadcrumbs
     *
     * @return Page
     */
    protected function _initAction()
    {
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE);

        return $resultPage;
    }

    /**
     * @return \Magento\Customer\Model\Attribute
     * @throws LocalizedException
     */
    protected function _initAttribute()
    {
        /** @var \Magento\Customer\Model\Attribute $attrObj */
        $attrObj = $this->_attrFactory->create()->setEntityTypeId($this->_getEntityType()->getId());

        return $attrObj;
    }

    /**
     * @param string $path
     * @param array $params
     *
     * @return Redirect
     */
    protected function returnResult($path = '', array $params = ['_current' => true])
    {
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath($path, $params);
    }
}
