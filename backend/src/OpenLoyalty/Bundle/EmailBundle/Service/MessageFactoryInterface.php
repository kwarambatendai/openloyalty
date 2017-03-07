<?php
/*
 * This file is part of the "misrmart" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 10.02.17 13:32
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenLoyalty\Bundle\EmailBundle\Service;

use OpenLoyalty\Bundle\EmailBundle\Model\MessageInterface;

/**
 * Interface MessageFactoryInterface.
 */
interface MessageFactoryInterface
{
    /**
     * Create message object.
     *
     * @return MessageInterface
     */
    public function create();
}
