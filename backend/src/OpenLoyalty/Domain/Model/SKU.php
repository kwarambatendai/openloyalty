<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Model;

use Broadway\Serializer\SerializableInterface;

/**
 * Class SKU.
 */
class SKU implements SerializableInterface
{
    /**
     * @var string
     */
    protected $code;

    /**
     * SKU constructor.
     *
     * @param string $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    public function __toString()
    {
        return $this->code;
    }

    /**
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self($data['code']);
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'code' => $this->getCode(),
        ];
    }
}
