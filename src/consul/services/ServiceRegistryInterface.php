<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace common\components\consul\services;

use common\components\consul\models\Service;

interface ServiceRegistryInterface
{
    public function register(Service $service);
    public function unregister(Service $service);
}
