<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

use indigerd\consul\models\KeyValue;

class ServiceKeyValue extends BaseService implements ServiceKeyValueInterface
{
    public function getValue($key)
    {
        $data = $this->request('get', '/v1/kv/'.$key, [], false);
        if ($data) {
            return KeyValue::fromJson($data)->getValue();
        }
    }

    public function setValue($key, $value)
    {
        $params['body'] = $value;
        return $this->put('/v1/kv/'.$key, $params);
    }

    public function deleteValue($key, $recurse = false)
    {
        $params = [];
        if ($recurse) {
            $params['query'] = ['recurse' => true];
        }
        return $this->delete('/v1/kv/'.$key, $params);
    }
}
