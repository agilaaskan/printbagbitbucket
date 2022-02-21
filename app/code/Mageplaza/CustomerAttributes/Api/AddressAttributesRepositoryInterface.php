<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
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

namespace Mageplaza\CustomerAttributes\Api;

/**
 * Interface AddressAttributesRepositoryInterface
 * @package Mageplaza\CustomerAttributes\Api
 */
interface AddressAttributesRepositoryInterface
{
    /**
     * @param string $cartId
     * @param string $addressType
     *
     * @return \Mageplaza\CustomerAttributes\Api\Data\FileResultInterface
     */
    public function guestUpload($cartId, $addressType);

    /**
     * @param string $cartId
     * @param string $addressType
     *
     * @return \Mageplaza\CustomerAttributes\Api\Data\FileResultInterface
     */
    public function upload($cartId, $addressType);
}
