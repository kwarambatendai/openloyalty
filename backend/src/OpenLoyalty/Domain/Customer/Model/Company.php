<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Model;

use Broadway\Serializer\SerializableInterface;
use Assert\Assertion as Assert;

/**
 * Class Company.
 */
class Company implements SerializableInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $nip;

    public function __construct($name, $nip)
    {
        Assert::notBlank($name);
        Assert::notBlank($nip);

        $this->name = $name;
        $this->nip = $nip;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNip()
    {
        return $this->nip;
    }

    /**
     * @param array $data
     *
     * @return mixed The object instance
     */
    public static function deserialize(array $data)
    {
        return new self($data['name'], $data['nip']);
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'name' => $this->getName(),
            'nip' => $this->getNip(),
        ];
    }
}
