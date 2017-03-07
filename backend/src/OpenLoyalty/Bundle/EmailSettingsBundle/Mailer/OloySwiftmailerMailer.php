<?php
/*
 * This file is part of the "misrmart" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 10.02.17 14:14
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenLoyalty\Bundle\EmailSettingsBundle\Mailer;

use OpenLoyalty\Bundle\EmailBundle\Mailer\OloySwiftmailerMailer as BaseMailer;
use OpenLoyalty\Domain\Email\ReadModel\DoctrineEmailRepositoryInterface;
use OpenLoyalty\Bundle\EmailBundle\Model\MessageInterface;
use Symfony\Bridge\Twig\TwigEngine;
use OpenLoyalty\Domain\Email\ReadModel\Email;
use Swift_Mailer;
use Twig_Environment;

/**
 * Class OloySwiftmailerMailer.
 */
class OloySwiftmailerMailer extends BaseMailer
{
    /**
     * @var DoctrineEmailRepositoryInterface
     */
    protected $emailRepository;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * {@inheritdoc}
     *
     * @param DoctrineEmailRepositoryInterface $emailRepository
     * @param Twig_Environment                 $twig
     */
    public function __construct(TwigEngine $twigEngine, Swift_Mailer $swiftmailer, DoctrineEmailRepositoryInterface $emailRepository, Twig_Environment $twig)
    {
        parent::__construct($twigEngine, $swiftmailer);

        $this->emailRepository = $emailRepository;
        $this->twig = $twig;
    }

    /**
     * {@inheritdoc}
     */
    protected function decorateMessage(MessageInterface $message)
    {
        $result = parent::decorateMessage($message);

        // decorate message with data from database
        if ($emailTemplate = $this->getEmailTemplate($message->getTemplate())) {
            $message->setSubject($emailTemplate->getSubject());
            $message->setSenderName($emailTemplate->getSenderName());
            $message->setSenderEmail($emailTemplate->getSenderEmail());

            $template = $this->twig->createTemplate($emailTemplate->getContent());
            $renderedContent = $template->render($message->getParams());
            $message->setContent($renderedContent);
        }

        return $result;
    }

    /**
     * @param $key
     *
     * @return Email|null
     */
    protected function getEmailTemplate($key)
    {
        return $this->emailRepository->getByKey($key);
    }
}
