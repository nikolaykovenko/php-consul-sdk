<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

class ServiceDiscovery extends BaseService implements ServiceDiscoveryInterface
{
    public function getServiceAddress($serviceName, $version = null, $default = null)
    {
        $list = $this->getServiceAddresses($serviceName, $version);
        if (!empty($list)) {
            $item = $list[array_rand($list)];
            return "http://$item->ServiceAddress:$item->ServicePort";
        }
        return $default;
    }

    public function getServiceAddresses($serviceName, $version = null)
    {
        $url = "/v1/catalog/service/$serviceName?passing";
        if ($version != null) {
            $url .= "&tag=v$version";
        }
        return $this->get($url);
    }
}
