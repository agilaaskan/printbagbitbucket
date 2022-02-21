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

namespace Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\Swatches\Model\Swatch;
use Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute;

/**
 * Class Save
 * @package Mageplaza\CustomerAttributes\Controller\Adminhtml\Attribute\Plugin
 */
class Save
{
    /**
     * @param Attribute\Save $subject
     * @param RequestInterface $request
     *
     * @return array
     */
    public function beforeDispatch(Attribute\Save $subject, RequestInterface $request)
    {
        $data = $request->getPostValue();
        if (isset($data['frontend_input'])) {
            switch ($data['frontend_input']) {
                case 'select_visual':
                case 'multiselect_visual':
                case 'textarea_visual':
                    $data[Swatch::SWATCH_INPUT_TYPE_KEY] = Swatch::SWATCH_INPUT_TYPE_VISUAL;
                    $data['frontend_input']              = str_replace('_visual', '', $data['frontend_input']);
                    $request->setPostValue($data);
                    break;
            }
        }

        return [$request];
    }
}
