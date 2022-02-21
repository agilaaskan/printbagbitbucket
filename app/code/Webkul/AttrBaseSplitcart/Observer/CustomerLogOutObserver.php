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

class CustomerLogOutObserver implements ObserverInterface
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
     * @var \Webkul\AttrBaseSplitcart\Cookie\Guestcart
     */
    private $guestCart;

    /**
     * @param AttrBaseLogger $attrBaseLogger
     * @param \Webkul\AttrBaseSplitcart\Helper\Data $helper
     * @param \Webkul\AttrBaseSplitcart\Cookie\Guestcart $guestCart
     */
    public function __construct(
        AttrBaseLogger $attrBaseLogger,
        \Webkul\AttrBaseSplitcart\Helper\Data $helper,
        \Webkul\AttrBaseSplitcart\Cookie\Guestcart $guestCart
    ) {
        $this->logger = $attrBaseLogger;
        $this->helper = $helper;
        $this->guestCart = $guestCart;
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
        try {
            $this->guestCart->delete();
            $this->guestCart->set(0, 3600);
        } catch (\Exception $e) {
            $this->attrBaseLogger->info("CustomerLogoutObserver execute : ".$e->getMessage());
        }
    }
}
