<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Email;

use Assert\Assertion as Assert;

/**
 * Class Email.
 */
class Email
{
    /**
     * @var EmailId
     */
    protected $emailId;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $senderName;

    /**
     * @var string
     */
    protected $senderEmail;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Email constructor.
     *
     * @param EmailId $emailId
     * @param string  $key
     * @param string  $subject
     * @param string  $content
     * @param string  $senderName
     * @param string  $senderEmail
     */
    public function __construct(
        EmailId $emailId,
        string $key,
        string $subject,
        string $content,
        string $senderName,
        string $senderEmail
    ) {
        Assert::uuid($emailId->__toString());
        Assert::notEmpty($key);
        Assert::notEmpty($subject);
        Assert::notEmpty($content);
        Assert::notEmpty($senderName);
        Assert::email($senderEmail);

        $this->emailId = $emailId;
        $this->key = $key;
        $this->subject = $subject;
        $this->content = $content;
        $this->senderName = $senderName;
        $this->senderEmail = $senderEmail;
        $this->updatedAt = new \DateTime('now');
    }

    /**
     * Create email instance.
     *
     * @param EmailId $id
     * @param array   $data
     *
     * @return Email
     */
    public static function create(EmailId $id, array $data)
    {
        return new self(
            $id,
            $data['key'],
            $data['subject'],
            $data['content'],
            $data['senderName'],
            $data['senderEmail']
        );
    }

    /**
     * @return EmailId
     */
    public function getEmailId(): EmailId
    {
        return $this->emailId;
    }

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @param string $senderName
     */
    public function setSenderName(string $senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * @param string $senderEmail
     */
    public function setSenderEmail(string $senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }
}
