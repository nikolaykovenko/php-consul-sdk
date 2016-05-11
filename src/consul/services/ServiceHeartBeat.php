<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace common\components\consul\services;

use common\components\consul\models\Check;

class ServiceHeartBeat extends ClientAwareService implements ServiceHeartBeatInterface
{
    protected $healthCheks = [];

    public function getRegisteredChecks()
    {
        $url    = "/v1/health/service/" . $this->service->getName();
        $data   = $this->get($url);
        $checks = [];
        foreach ($data['Checks'] as $check) {
            $checks[] = (new Check())
                ->setId($check['CheckID'])
                ->setName($check['Name'])
                ->setStatus($check['Status'])
                ->setNotes($check['Notes'])
                ->setOutput($check['Output'])
            ;

        }
        return $checks;
    }

    public function send()
    {
        $url = "/v1/agent/check/pass/$this->id";
        return $this->get($url);
    }

    public function respond()
    {
        try {
            $health = $this->doHealthChecks();
            if (!$health) {
                throw  new \Exception;
            }
            $this->logger->info('Health check passed for service:' . $this->service->getName());
        } catch (\Exception $e) {
            $this->logger->warning('Health check failed for service:' . $this->service->getName());
            $health = false;
        }
        return $health;
    }

    public function addHealthCheck($healthCheck)
    {
        if (!is_callable($healthCheck)) {
            throw new \InvalidArgumentException('Health check must be callable');
        }
        $this->healthCheks[] = $healthCheck;
    }

    public function setHealtChecks(array $healtChecks)
    {
        $this->healthChecks = [];
        foreach ($healtChecks as $healthCheck) {
            $this->addHealthCheck($healthCheck);
        }
    }

    protected function doHealthChecks()
    {
        foreach ($this->healthCheks as $healthChek) {
            if (!call_user_func($healthChek)) {
                return false;
            }
        }
        return true;
    }
}
