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

namespace Mageplaza\CustomerAttributes\Api\Data;

/**
 * Interface FileResultInterface
 * @package Mageplaza\CustomerAttributes\Api\Data
 */
interface FileResultInterface
{
    const ERROR = 'error';
    const FILE  = 'file';
    const NAME  = 'name';
    const SIZE  = 'size';
    const TYPE  = 'type';
    const URL   = 'url';

    /**
     * @return string
     */
    public function getError();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setError($value);

    /**
     * @return string
     */
    public function getFile();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFile($value);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSize($value);

    /**
     * @return string
     */
    public function getSize();

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUrl($value);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setType($value);
}
