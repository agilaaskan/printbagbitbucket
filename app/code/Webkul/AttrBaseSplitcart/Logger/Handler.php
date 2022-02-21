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
namespace Webkul\AttrBaseSplitcart\Logger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    /**
     * Logging level.
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @var int
     */
    public $loggerType = AttrBaseLogger::INFO;

    /**
     * File name.
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     * @var string
     */
    public $fileName = '/var/log/attrbasesplitcart.log';
}
