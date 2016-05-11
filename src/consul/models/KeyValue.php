<?php
/**
 * @author Alexander Stepanenko <alex.stepanenko@gmail.com>
 */

namespace indigerd\consul\models;

class KeyValue
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string[]
     */
    protected $flags;

    /**
     * @var int
     */
    protected $createIndex;

    /**
     * @var int
     */
    protected $modifyIndex;

    public static function fromJson($json)
    {
        $data = json_decode($json, true);
        $keyValue = new self();
        $keyValue->createIndex = $data[0]['CreateIndex'];
        $keyValue->modifyIndex = $data[0]['ModifyIndex'];
        $keyValue->key = $data[0]['Key'];
        $keyValue->flags = $data[0]['Flags'];
        $keyValue->value = base64_decode($data[0]['Value']);
        return $keyValue;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
}
