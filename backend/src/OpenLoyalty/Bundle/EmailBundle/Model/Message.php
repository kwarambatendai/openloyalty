<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\EmailBundle\Model;

/**
 * Class Message.
 */
class Message implements MessageInterface
{
    /**
     * @var string
     */
    protected $subject;

    /**
     * @var string
     */
    protected $recipientEmail;

    /**
     * @var string
     */
    protected $recipientName;

    /**
     * @var string
     */
    protected $senderEmail;

    /**
     * @var string
     */
    protected $senderName;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $plainContent;

    protected $template;

    protected $params = [];

    /**
     * {@inheritdoc}
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientEmail(): string
    {
        return $this->recipientEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientEmail(string $recipientEmail)
    {
        $this->recipientEmail = $recipientEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientName(): string
    {
        return $this->recipientName;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientName(string $recipientName)
    {
        $this->recipientName = $recipientName;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderEmail(string $senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderName(): string
    {
        return $this->senderName;
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderName(string $senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getPlainContent()
    {
        return $this->plainContent;
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainContent(string $plainContent)
    {
        $this->plainContent = $plainContent;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate(string $template = null)
    {
        $this->template = $template;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * {@inheritdoc}
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * {@inheritdoc}
     */
    public function getParams(): array
    {
        return $this->params;
    }
}
