<?php

namespace Post\Box\Sdk;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Common\HttpMethodsClientInterface;
use Http\Client\Common\Plugin;
use Http\Client\Common\PluginClientFactory;
use Http\Discovery\Psr17FactoryDiscovery;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class ClientBuilder
{
    private ClientInterface $httpClient;

    private RequestFactoryInterface $requestFactoryInterface;

    private StreamFactoryInterface $streamFactoryInterface;

    private array $plugins = [];

    public function __construct(
        ClientInterface $httpClient = null,
        RequestFactoryInterface $requestFactoryInterface = null,
        StreamFactoryInterface $streamFactoryInterface = null
    ) {
        $this->httpClient = $httpClient ?: GuzzleAdapter::createWithConfig([]);;
        $this->requestFactoryInterface = $requestFactoryInterface ?: Psr17FactoryDiscovery::findRequestFactory();
        $this->streamFactoryInterface = $streamFactoryInterface ?: Psr17FactoryDiscovery::findStreamFactory();
    }

    public function addPlugin(Plugin $plugin): void
    {
        $this->plugins[] = $plugin;
    }

    public function getHttpClient(): HttpMethodsClientInterface
    {
        $pluginClient = (new PluginClientFactory())->createClient($this->httpClient, $this->plugins);

        return new HttpMethodsClient(
            $pluginClient,
            $this->requestFactoryInterface,
            $this->streamFactoryInterface
        );
    }
}