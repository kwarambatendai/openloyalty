<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 01.02.17 15:01
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenLoyalty\Domain\Email;

use Assert\Assertion as Assert;
use OpenLoyalty\Domain\Identifier;

/**
 * Class EmailId.
 */
class EmailId implements Identifier
{
    /**
     * @var string
     */
    private $id;

    /**
     * EmailId constructor.
     *
     * @param string $id
     */
    public function __construct($id)
    {
        Assert::string($id);
        Assert::uuid($id);

        $this->id = $id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id;
    }
}
