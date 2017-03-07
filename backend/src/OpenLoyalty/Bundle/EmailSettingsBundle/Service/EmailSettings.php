<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\EmailSettingsBundle\Service;

use OpenLoyalty\Domain\Email\ReadModel\Email;
use Symfony\Bundle\TwigBundle\Loader\FilesystemLoader;

/**
 * Class EmailSettings.
 */
class EmailSettings
{
    /**
     * Emails list.
     *
     * @var array
     */
    protected $emails = [];

    /**
     * FilesystemLoader.
     *
     * @var FilesystemLoader
     */
    protected $filesystemLoader;

    /**
     * Twig.
     *
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * OloyEmailSettings constructor.
     *
     * @param array             $emails
     * @param FilesystemLoader  $filesystemLoader
     * @param \Twig_Environment $twig
     */
    public function __construct(array $emails, FilesystemLoader $filesystemLoader, \Twig_Environment $twig)
    {
        $this->emails = $emails;
        $this->filesystemLoader = $filesystemLoader;
        $this->twig = $twig;
    }

    /**
     * Get emails parameter.
     *
     * @return array
     */
    public function getEmailsParameter()
    {
        foreach ($this->emails as &$settings) {
            $settings['content'] = '';

            // fill with default template if empty
            if ($this->filesystemLoader->exists($settings['template'])) {
                $sourceContext = $this->filesystemLoader->getSourceContext($settings['template']);
                $settings['content'] = $sourceContext->getCode();
            }
        }

        return $this->emails;
    }

    /**
     * Get additional params.
     *
     * @param Email $email
     *
     * @return array
     */
    public function getAdditionalParams(Email $email)
    {
        $additionalParams = [];

        foreach ($this->emails as $emailConfig) {
            if ($emailConfig['template'] == $email->getKey()) {
                $additionalParams['variables'] = $emailConfig['variables'];

                $template = $this->twig->createTemplate($email->getContent());
                $additionalParams['preview'] = $template->render(
                    array_combine($additionalParams['variables'], $additionalParams['variables'])
                );
            }
        }

        return $additionalParams;
    }
}
