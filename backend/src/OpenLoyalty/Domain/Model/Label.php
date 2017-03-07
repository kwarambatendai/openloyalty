<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Model;

use Broadway\Serializer\SerializableInterface;

/**
 * Class Label.
 */
class Label implements SerializableInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $value;

    /**
     * Label constructor.
     *
     * @param string $key
     * @param string $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self($data['key'], $data['value']);
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'key' => $this->key,
            'value' => $this->value,
        ];
    }
}
