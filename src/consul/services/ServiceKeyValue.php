<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

use indigerd\consul\models\KeyValue;

class ServiceKeyValue extends BaseService implements ServiceKeyValueInterface
{
    public function getKeyValue(string $key, $token = null) : KeyValue
    {
        $data = $this->request('get', '/v1/kv/' . $key, $this->addTokenHeader($token), false);
        if (!$data) {
            return null;
        }
        $data = json_decode($data, true);
        $keyValue = new KeyValue;
        $keyValue->setKey($data[0]['Key']);
        $keyValue->setValue(base64_decode($data[0]['Value']));
        return $keyValue;
    }

    public function setKeyValue(KeyValue $keyValue, $token = null)
    {
        $params['body'] = $keyValue->getValue();
        return $this->put('/v1/kv/' . $keyValue->getKey(), $this->addTokenHeader($token, $params));
    }

    public function deleteKeyValue(KeyValue $keyValue, bool $recurse = false, $token = null)
    {
        $params = [];
        if ($recurse) {
            $params['query'] = ['recurse' => true];
        }
        return $this->delete('/v1/kv/' . $keyValue->getKey(), $this->addTokenHeader($token, $params));
    }

    /**
     * Adds consul auth token to request headers
     * @param string|null $token
     * @param array $params
     * @return array
     */
    private function addTokenHeader($token, array $params = [])
    {
        if ($token && empty($params['headers']['X-Consul-Token'])) {
            $params['headers']['X-Consul-Token'] = $token;
        }

        return $params;
    }
}
