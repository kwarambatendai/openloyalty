<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
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
