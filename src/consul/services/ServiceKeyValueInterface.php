<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

use indigerd\consul\models\KeyValue;

interface ServiceKeyValueInterface
{
    public function getKeyValue(string $key, $token = null) : KeyValue;
    public function setKeyValue(KeyValue $keyValue, $token = null);
    public function deleteKeyValue(KeyValue $keyValue, bool $recurse, $token = null);
}
