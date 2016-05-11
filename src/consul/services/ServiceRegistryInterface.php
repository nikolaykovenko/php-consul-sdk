<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

use indigerd\consul\models\Service;

interface ServiceRegistryInterface
{
    public function register(Service $service);
    public function unregister(Service $service);
}
