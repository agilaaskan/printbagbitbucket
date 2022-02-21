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

namespace Mageplaza\CustomerAttributes\Model;

use Exception;
use Magento\Customer\Model\Attribute;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class AbstractSales
 * @package Mageplaza\CustomerAttributes\Model
 */
abstract class AbstractSales extends AbstractModel
{
    /**
     * @param Attribute $attribute
     *
     * @return $this
     * @throws LocalizedException
     */
    public function createAttribute(Attribute $attribute)
    {
        $this->_getResource()->createAttribute($attribute);

        return $this;
    }

    /**
     * @param Attribute $attribute
     *
     * @return $this
     * @throws LocalizedException
     */
    public function deleteAttribute(Attribute $attribute)
    {
        $this->_getResource()->deleteAttribute($attribute);

        return $this;
    }

    /**
     * @param AbstractModel $sales
     *
     * @return $this
     */
    public function attachAttributeData(AbstractModel $sales)
    {
        $sales->addData($this->getData());

        return $this;
    }

    /**
     * @param AbstractModel $sales
     *
     * @return $this
     * @throws Exception
     */
    public function saveAttributeData(AbstractModel $sales)
    {
        $this->addData($sales->getData())->setId($sales->getId())->save();

        return $this;
    }

    /**
     * @param DataObject[] $entities
     *
     * @return array
     * @throws LocalizedException
     */
    public function attachDataToSalesOrder(array $entities)
    {
        return $this->_getResource()->attachDataToSalesOrder($entities);
    }
}
