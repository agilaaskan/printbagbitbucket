<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerBR\Model\Source\Config;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

class PersonType extends AbstractSource
{
    const PERSON_TYPE_NATURAL = 'natural';
    const PERSON_TYPE_LEGAL = 'legal';

    /**
     * @var array
     */
    private $options = [];

    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        if (!$this->options) {
            $this->options = [
                ['label' => __('Natural Person'), 'value' => static::PERSON_TYPE_NATURAL],
                ['label' => __('Legal Person'), 'value' => static::PERSON_TYPE_LEGAL]
            ];
        }

        return $this->options;
    }
}
