<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 02.02.17 14:33
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenLoyalty\Domain\Email\SystemEvent;

use OpenLoyalty\Domain\Email\EmailId;

/**
 * Class EmailSystemEvent.
 */
class EmailSystemEvent
{
    /**
     * @var EmailId
     */
    protected $emailId;

    /**
     * EmailCreatedSystemEvents constructor.
     *
     * @param EmailId $emailId
     */
    public function __construct(EmailId $emailId)
    {
        $this->emailId = $emailId;
    }

    /**
     * @return EmailId
     */
    public function getEmailId()
    {
        return $this->emailId;
    }
}
