<?php
require '../vendor/autoload.php';



$factory = new \indigerd\consul\ServiceFactory();
/* @var \indigerd\consul\services\ServiceKeyValue $kv */
$kv = $factory->get('kv');

$model = new \indigerd\consul\models\KeyValue();

$model->setKey('test-value');
$model->setValue('Some test content');

$kv->setKeyValue($model);

$v = $kv->getKeyValue($model->getKey());

var_dump($v);

/* @var \indigerd\consul\services\ServiceRegistry $registry */
$registry = $factory->get('registry');

$service = new \indigerd\consul\models\Service();
$service->setName('TestMicroService');
$service->setAddress('192.168.44.177');
$service->setPort(80);
$version = 1;
$service->addTag('v' . $version);
$registry->register($service);

/* @var \indigerd\consul\services\ServiceDiscovery $discovery */
$discovery = $factory->get('discovery');

$address = $discovery->getServiceAddress($service->getName(), 1);

var_dump($address);

class ConsulHeartBeatCommand
{
    const EXIT_CODE_NORMAL = 0;
    const EXIT_CODE_ERROR = 1;
    const EXIT_CODE_CONSUL_HEARTBEAT_FAIL = 2;

    public $defaultAction = 'respond';

    public function run($name, $id = '')
    {
        $service = new \indigerd\consul\models\Service();
        $service->setName($name);
        if (!empty($id)) {
            $service->setId($id);
        }
        $factory = new \indigerd\consul\ServiceFactory();
        $heartBeat = $factory->get('health');
        $heartBeat->addHealthCheck(function () {
            return (4 === 2 + 2);
        });
        try {
            $health = $heartBeat->run($service);
        } catch (\Exception $e) {
            $health = false;
        }
        if ($health) {
            return self::EXIT_CODE_NORMAL;
        }
        return self::EXIT_CODE_CONSUL_HEARTBEAT_FAIL;
    }
}

$command = new ConsulHeartBeatCommand;

$health = $command->run($service->getName());

var_dump('Health:' . $health);