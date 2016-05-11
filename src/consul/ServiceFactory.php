<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace common\components\consul;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class ServiceFactory
{
    protected static $services = array(
        'discovery' => 'common\components\consul\services\ServiceDiscovery',
        'health'    => 'common\components\consul\services\ServiceHeartBeat',
        'registry'  => 'common\components\consul\services\ServiceRegistry',
        'kv'        => 'common\components\consul\services\ServiceKeyValue',
    );

    /** @var Client  */
    private $client;

    /** @var LoggerInterface  */
    private $logger;

    private $consulAddress;

    public function __construct(
        Client $client = null,
        LoggerInterface $logger = null,
        $consulAddress = null
    ) {
        $this->client = $client;
        $this->logger = $logger;
        $this->consulAddress = $consulAddress;
    }

    public function get($service, $params = [])
    {
        if (!array_key_exists($service, static::$services)) {
            throw new \InvalidArgumentException(sprintf('The service "%s" is not available.', $service));
        }
        $class = static::$services[$service];
        $args = [$this->client, $this->logger, $this->consulAddress];
        foreach ($params as $param) {
            $args[] = $param;
        }
        return new $class(...$args);
    }
}
