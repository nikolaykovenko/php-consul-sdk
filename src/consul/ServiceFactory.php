<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use indigerd\consul\services\BaseService;

class ServiceFactory
{
    protected $services = array(
        'discovery' => 'indigerd\consul\services\ServiceDiscovery',
        'health'    => 'indigerd\consul\services\ServiceHeartBeat',
        'registry'  => 'indigerd\consul\services\ServiceRegistry',
        'kv'        => 'indigerd\consul\services\ServiceKeyValue',
    );

    /** @var Client  */
    protected $client;

    /** @var LoggerInterface  */
    protected $logger;

    protected $consulAddress;

    /**
     * @var string
     */
    protected $consulAuthToken;

    public function __construct(
        Client $client = null,
        LoggerInterface $logger = null,
        string $consulAddress = null,
        array $services = [],
        string $consulAuthToken = null
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->consulAddress = $consulAddress;
        $this->services = array_merge($this->services, $services);
        $this->consulAuthToken = $consulAuthToken;
    }

    public function get(string $service, array $params = []) : BaseService
    {
        if (!array_key_exists($service, $this->services)) {
            throw new \InvalidArgumentException(sprintf('The service "%s" is not available.', $service));
        }
        $class = $this->services[$service];
        $args = [$this->client, $this->logger, $this->consulAddress, $this->consulAuthToken];
        foreach ($params as $param) {
            $args[] = $param;
        }
        return new $class(...$args);
    }
}
