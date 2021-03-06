<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category  BSS
 * @package   Bss_LayerNavigation
 * @author    Extension Team
 * @copyright Copyright (c) 2018-2019 BSS Commerce Co. ( http://bsscommerce.com )
 * @license   http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\LayerNavigation\Block;

use Magento\Customer\Model\Context;

class InfiniteScroll extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    public $moduleManager;

    /**
     * @var \Bss\LayerNavigation\Model\InfiniteScrollFactory
     */
    protected $helperbssInfiniteScroll;

    /**
     * InfiniteScroll constructor.
     * @param Template\Context $context
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Bss\LayerNavigation\Model\InfiniteScrollFactory $helperbssInfiniteScroll
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Module\Manager $moduleManager,
        \Bss\LayerNavigation\Model\InfiniteScrollFactory $helperbssInfiniteScroll,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleManager = $moduleManager;
        $this->helperbssInfiniteScroll = $helperbssInfiniteScroll;
    }

    /**
     * @return \Bss\LayerNavigation\Helper\Data
     */
    public function isBssInfiniteScrollEnabled()
    {
        return $this->moduleManager->isEnabled('Bss_InfiniteScroll');
    }

    public function getBlockBssInfiniteScroll()
    {
        return $this->helperbssInfiniteScroll->create();
    }
}
