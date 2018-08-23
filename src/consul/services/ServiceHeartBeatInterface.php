<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

use indigerd\consul\models\Check;
use indigerd\consul\models\Service;

interface ServiceHeartBeatInterface
{
    public function passCheck(Check $check);
    public function failCheck(Check $check);
    public function getRegisteredChecks(Service $service) : array;
    public function run(Service $service) : bool;
    public function addHealthCheck(callable $healthCheck);
    public function setHealthChecks(array $healthChecks);
}
