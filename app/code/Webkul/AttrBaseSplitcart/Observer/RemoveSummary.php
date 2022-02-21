<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_AttrBaseSplitcart
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\AttrBaseSplitcart\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Webkul\AttrBaseSplitcart\Logger\AttrBaseLogger;

class RemoveSummary implements ObserverInterface
{
    /**
     * @var \Webkul\AttrBaseSplitcart\Helper\Data
     */
    private $helper;

    /**
     * @var AttrBaseLogger
     */
    private $attrBaseLogger;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @param AttrBaseLogger $attrBaseLogger
     * @param \Webkul\AttrBaseSplitcart\Helper\Data $helper
     */
    public function __construct(
        AttrBaseLogger $attrBaseLogger,
        \Webkul\AttrBaseSplitcart\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->logger = $attrBaseLogger;
        $this->helper = $helper;
        $this->request = $request;
    }

    /**
     * [executes when layout_generate_blocks_after event hit,
     * and used to remove default summary block]
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        $page = $this->request->getFullActionName();
        if ($this->helper->checkAttributesplitcartStatus() && $page=="checkout_cart_index") {
            $layout = $observer->getLayout();
            $layout->unsetElement("cart.summary");
        }
    }
}
