<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\services;

interface ServiceKeyValueInterface
{
    public function getValue($key);
    public function setValue($key, $value);
    public function deleteValue($key);
}
