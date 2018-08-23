<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

use indigerd\consul\models\KeyValue;

class ServiceKeyValue extends BaseService implements ServiceKeyValueInterface
{
    public function getKeyValue(string $key) : KeyValue
    {
        $data = $this->request('get', '/v1/kv/' . $key, [], false);
        if (!$data) {
            return null;
        }
        $data = json_decode($data, true);
        $keyValue = new KeyValue;
        $keyValue->setKey($data[0]['Key']);
        $keyValue->setValue(base64_decode($data[0]['Value']));
        return $keyValue;
    }

    public function setKeyValue(KeyValue $keyValue)
    {
        $params['body'] = $keyValue->getValue();
        return $this->put('/v1/kv/' . $keyValue->getKey(), $params);
    }

    public function deleteKeyValue(KeyValue $keyValue, bool $recurse = false)
    {
        $params = [];
        if ($recurse) {
            $params['query'] = ['recurse' => true];
        }
        return $this->delete('/v1/kv/' . $keyValue->getKey(), $params);
    }
}
