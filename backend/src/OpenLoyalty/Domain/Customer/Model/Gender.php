<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Customer\Model;

/**
 * Class Gender.
 */
class Gender
{
    const MALE = 'male';
    const FEMALE = 'female';

    /**
     * @var string
     */
    protected $type;

    public static function male()
    {
        return new self(static::MALE);
    }

    public static function female()
    {
        return new self(static::FEMALE);
    }

    public function __construct($type)
    {
        if ($type != static::MALE && $type != static::FEMALE) {
            throw new \InvalidArgumentException('Gender should be male or female');
        }

        $this->type = $type;
    }

    public function __toString()
    {
        return $this->type;
    }

    public function isMale()
    {
        return $this->type == static::MALE;
    }

    public function isFemale()
    {
        return $this->type == static::FEMALE;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
