<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer\Model;

/**
 * Class Coupon.
 */
class Coupon
{
    /**
     * @var string
     */
    protected $code;

    /**
     * Coupon constructor.
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
}
