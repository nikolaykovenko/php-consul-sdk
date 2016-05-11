<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

interface ServiceHeartBeatInterface
{
    public function send();
    public function respond();
    public function addHealthCheck($healthCheck);
    public function setHealtChecks(array $healtChecks);
}
