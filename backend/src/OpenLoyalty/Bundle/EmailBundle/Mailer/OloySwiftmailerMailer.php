<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\EmailBundle\Mailer;

use OpenLoyalty\Bundle\EmailBundle\Model\MessageInterface;
use Symfony\Bridge\Twig\TwigEngine;

/**
 * Class OloySwiftmailerMailer.
 */
class OloySwiftmailerMailer implements OloyMailer
{
    /**
     * @var \Swift_Mailer
     */
    protected $swiftmailer;

    /**
     * @var TwigEngine
     */
    protected $twigEngine;

    /**
     * OloySwiftmailerMailer constructor.
     *
     * @param TwigEngine    $twigEngine
     * @param \Swift_Mailer $swiftmailer
     */
    public function __construct(TwigEngine $twigEngine, \Swift_Mailer $swiftmailer)
    {
        $this->twigEngine = $twigEngine;
        $this->swiftmailer = $swiftmailer;
    }

    /**
     * {@inheritdoc}
     */
    public function send(MessageInterface $message)
    {
        $this->decorateMessage($message);

        $msg = \Swift_Message::newInstance()
                             ->setSubject($message->getSubject())
                             ->setFrom($message->getSenderEmail(), $message->getSenderName())
                             ->setTo($message->getRecipientEmail(), $message->getRecipientName())
                             ->setBody($message->getContent(), 'text/html')
                             ->addPart($message->getPlainContent() ?: strip_tags($message->getContent()), 'text/plain');

        $sent = $this->swiftmailer->send($msg);

        return $sent > 0;
    }

    /**
     * @param MessageInterface $message
     */
    protected function decorateMessage(MessageInterface $message)
    {
        // nothing to do
        if (!$this->twigEngine->exists($message->getTemplate())) {
            return;
        }

        $templateContent = $this->renderTemplateContent($message);

        $message->setContent($templateContent);
    }

    /**
     * @param MessageInterface $message
     *
     * @return string
     */
    protected function renderTemplateContent(MessageInterface $message): string
    {
        return $this->twigEngine->render($message->getTemplate(), $message->getParams());
    }
}
