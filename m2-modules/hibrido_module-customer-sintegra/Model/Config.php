<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerSintegra\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    /**
     * XML Config Paths
     */
    const XML_CONFIG_API_PATH = 'sintegra/api/';
    const XML_CONFIG_API_MODE_PATH = self::XML_CONFIG_API_PATH . 'mode';
    const XML_CONFIG_API_SANDBOX_ACCESS_TOKEN_PATH = self::XML_CONFIG_API_PATH . 'sandbox_access_token';
    const XML_CONFIG_API_PRODUCTION_ACCESS_TOKEN_PATH = self::XML_CONFIG_API_PATH . 'production_access_token';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return null|string
     */
    public function getApiMode(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_API_MODE_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isApiModeSandbox(): bool
    {
        return $this->getApiMode() === 'sandbox';
    }

    /**
     * @return bool
     */
    public function isApiModeProduction(): bool
    {
        return $this->getApiMode() === 'production';
    }

    /**
     * @return null|string
     */
    public function getApiSandboxAccessToken(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_API_SANDBOX_ACCESS_TOKEN_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return null|string
     */
    public function getApiProductionAccessToken(): ?string
    {
        return $this->scopeConfig->getValue(self::XML_CONFIG_API_PRODUCTION_ACCESS_TOKEN_PATH, ScopeInterface::SCOPE_STORE);
    }
}