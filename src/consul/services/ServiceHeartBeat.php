<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

use indigerd\consul\models\Check;
use indigerd\consul\models\Service;

class ServiceHeartBeat extends BaseService implements ServiceHeartBeatInterface
{
    protected $healthChecks = [];

    public function getRegisteredChecks(Service $service) : array
    {
        $url    = "/v1/health/service/" . $service->getName();
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

    public function passCheck(Check $check)
    {
        $url = "/v1/agent/check/pass/$check->getId()";
        return $this->put($url);
    }

    public function failCheck(Check $check)
    {
        $url = "/v1/agent/check/fail/$check->getId()";
        return $this->put($url);
    }

    public function run(Service $service) : bool
    {
        try {
            foreach ($this->healthChecks as $healthChek) {
                $result = call_user_func($healthChek);
                if (!$result) {
                    throw new \RuntimeException('Healthcheck failed');
                }
            }
            $this->logger->info('Health check passed for service:' . $service->getName());
            $health = true;
        } catch (\Exception $e) {
            $this->logger->warning('Health check failed for service:' . $service->getName());
            $health = false;
        }
        return $health;
    }

    public function addHealthCheck(callable $healthCheck) : ServiceHeartBeat
    {
        $this->healthChecks[] = $healthCheck;
        return $this;
    }

    public function setHealthChecks(array $healthChecks) : ServiceHeartBeat
    {
        $this->healthChecks = [];
        foreach ($healthChecks as $healthCheck) {
            $this->addHealthCheck($healthCheck);
        }
        return $this;
    }
}
