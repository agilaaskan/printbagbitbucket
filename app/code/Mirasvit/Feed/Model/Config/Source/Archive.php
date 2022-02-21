<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.1.24
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Feed\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Archive implements ArrayInterface
{
    /**
     * @param bool $empty
     * @return array
     */
    public function toOptionArray($empty = false)
    {
        $result = [];

        if ($empty) {
            $result[] = [
                'label' => __('Disabled'),
                'value' => '',
            ];
        }

        $result[] = [
            'label' => __('ZIP (*.zip)'),
            'value' => 'zip',
        ];

        return $result;
    }
}
