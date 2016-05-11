<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace common\components\consul\services;

use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;
use common\components\consul\models\Service;

class ClientAwareService extends BaseService
{
    protected $service;

    public function __construct(
        Client $client = null,
        LoggerInterface $logger = null,
        $consulAddress = null,
        Service $service
    ) {
        $this->service = $service;
        parent::__construct($client, $logger, $consulAddress);
    }
}
