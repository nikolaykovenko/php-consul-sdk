<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace common\components\consul\services;

interface ServiceDiscoveryInterface
{
    public function getServiceAddress($serviceName, $version = null);
    public function getServiceAddresses($serviceName, $version = null);
}
