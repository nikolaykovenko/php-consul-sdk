<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

interface ServiceDiscoveryInterface
{
    public function getServiceAddress(string $serviceName, string $version = null);
    public function getServiceAddresses(string $serviceName, string $version = null);
}
