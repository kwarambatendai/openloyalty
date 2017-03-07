<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 01.02.17 14:34
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenLoyalty\Domain\Email\Command;

use Assert\Assertion as Assert;
use OpenLoyalty\Domain\Email\EmailId;

/**
 * Class EmailCommand.
 */
class EmailCommand
{
    /**
     * @var EmailId
     */
    protected $emailId;

    /**
     * EmailCommand constructor.
     *
     * @param EmailId $emailId
     */
    public function __construct(EmailId $emailId)
    {
        $this->emailId = $emailId;
    }

    /**
     * Get email id.
     *
     * @return EmailId
     */
    public function getEmailId()
    {
        return $this->emailId;
    }

    /**
     * Validate command.
     *
     * @param array $data
     */
    protected function validateCommand(array $data)
    {
        Assert::uuid($this->emailId->__toString());
        Assert::notEmpty($data['key']);
        Assert::notEmpty($data['subject']);
        Assert::notEmpty($data['content']);
        Assert::notEmpty($data['senderName']);
        Assert::notEmpty($data['senderEmail']);
    }
}
