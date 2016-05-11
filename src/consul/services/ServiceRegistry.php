<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

use indigerd\consul\models\Service;

class ServiceRegistry extends BaseService implements ServiceRegistryInterface
{
    public function register(Service $service)
    {
        $url = '/v1/agent/service/register';
        $params['body'] = json_encode([
            'Name' => $service->getName(),
            'ID'   => $service->getId(),
            'Port' => $service->getPort(),
            'Tags' => $service->getTags(),
        ]);
        return $this->put($url, $params);
    }

    public function unregister(Service $service)
    {
        $url = "/v1/agent/service/deregister/" . $service->getId();
        return $this->delete($url);
    }
}
