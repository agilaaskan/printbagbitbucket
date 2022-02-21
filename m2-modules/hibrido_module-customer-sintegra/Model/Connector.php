<?php
/**
 * Copyright © Hibrido. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Hibrido\CustomerSintegra\Model;

use Magento\Framework\HTTP\ClientInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\ClientFactory;

/**
 * @see https://www.sintegraws.com.br/
 */
class Connector
{
    const API_BASE_URL = 'https://www.sintegraws.com.br/api/v1/execute-api.php';
    const API_PLUGIN = 'RF';

    /**
     * @var Config
     */
    private $config;

    /**
     * @var ClientFactory
     */
    private $httpClientFactory;

    /**
     * @var ConnectorResponseFactory
     */
    private $connectorResponseFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Config $config
     * @param ClientFactory $httpClientFactory
     * @param ConnectorResponseFactory $connectorResponseFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        ClientFactory $httpClientFactory,
        ConnectorResponseFactory $connectorResponseFactory,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->httpClientFactory = $httpClientFactory;
        $this->connectorResponseFactory = $connectorResponseFactory;
        $this->logger = $logger;
    }

    /**
     * @param string $cnpj
     * @return ConnectorResponse
     * @throws LocalizedException
     */
    public function execute(string $cnpj): ConnectorResponse
    {
        //Get fake CNPJ if in sandbox.
        $cnpj = $this->validateCnpjForSandbox($cnpj);

        //Remove not numbers.
        $cnpj = $this->stripNotNumbers($cnpj);

        //Get HTTP Client.
        $client = $this->getHttpClient();

        //Generate the URI early so we can log it.
        //Explicitly let the LocalizedException bubble up.
        $uri = $this->getUri($cnpj);

        //Log the request.
        $this->logger->info('Customer Sintegra Request.', ['uri' => $uri]);

        //It is not explicit set in the interface, but, this call may throw an \Exception.
        try {
            $client->get($uri);
        } catch (\Exception $e) {
            //Log fail response.
            $this->logger->info('Customer Sintegra Fail Response.', [
                'exceptionCode' => $e->getCode(),
                'exceptionMessage' => $e->getMessage()
            ]);

            //Throw standarized exception.
            throw new LocalizedException(__($e->getMessage()));
        }

        //We can only get the body from the stream once.
        $body = $client->getBody();

        //Log successful response.
        $this->logger->info('Customer Sintegra Success Response.', ['body' => $body]);

        /** @noinspection PhpComposerExtensionStubsInspection */
        $body = json_decode($body, true);

        //Create a new response object.
        try {
            $response = $this->connectorResponseFactory->create(['data' => $body]);
        } catch (InputException $e) {
            //Log fail response.
            $this->logger->info('Customer Sintegra Fail Response.', [
                'exceptionCode' => $e->getCode(),
                'exceptionMessage' => $e->getMessage()
            ]);

            throw new LocalizedException(__($e->getMessage()));
        }

        return $response;
    }

    /**
     * @param string $cnpj
     * @return string
     */
    private function validateCnpjForSandbox(string $cnpj): string
    {
        if ($this->config->isApiModeSandbox()) {
            $cnpj = '06.990.590/0001-23';
        }

        return $cnpj;
    }

    /**
     * @param string $string
     * @return string
     */
    private function stripNotNumbers(string $string): string
    {
        return preg_replace('#\D#', '', $string);
    }

    /**
     * @return ClientInterface
     */
    private function getHttpClient(): ClientInterface
    {
        $client = $this->httpClientFactory->create();
        $client->addHeader('Accept', 'application/json');
        $client->addHeader('Content-Type', 'application/json');

        return $client;
    }

    /**
     * @param string $cnpj
     * @return string
     * @throws LocalizedException
     */
    private function getUri(string $cnpj): string
    {
        //Explicitly let the LocalizedException bubble up.
        $query = http_build_query([
            'token' => $this->getToken(),
            'cnpj' => $cnpj,
            'plugin' => self::API_PLUGIN
        ]);

        return self::API_BASE_URL .'?'. $query;
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    private function getToken(): string
    {
        if ($this->config->isApiModeSandbox()) {
            return $this->config->getApiSandboxAccessToken();
        }

        if ($this->config->isApiModeProduction()) {
            return $this->config->getApiProductionAccessToken();
        }

        throw new LocalizedException(__('Invalid Sintegra Api Operation Mode.'));
    }
}