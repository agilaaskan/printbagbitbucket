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

namespace Mageplaza\CustomerAttributes\Model\Address;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Mageplaza\CustomerAttributes\Model\AbstractSales;

/**
 * Class AbstractAddress
 * @package Mageplaza\CustomerAttributes\Model\Address
 */
abstract class AbstractAddress extends AbstractSales
{
    /**
     * Attach data to models
     *
     * @param DataObject[] $entities
     *
     * @return $this
     * @throws LocalizedException
     */
    public function attachDataToEntities(array $entities)
    {
        $this->_getResource()->attachDataToEntities($entities);

        return $this;
    }

    /**
     * @param array $entities
     * @param string $prefix
     *
     * @return array
     * @throws LocalizedException
     */
    public function attachDataToCustomerAddress(array $entities, $prefix = '')
    {
        return $this->_getResource()->attachDataToCustomerAddress($entities, $prefix);
    }
}
