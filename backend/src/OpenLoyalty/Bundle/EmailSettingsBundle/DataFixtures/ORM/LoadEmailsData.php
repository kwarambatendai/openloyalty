<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 31.01.17 13:14
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenLoyalty\Bundle\EmailSettingsBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OpenLoyalty\Domain\Email\Command\CreateEmail;
use OpenLoyalty\Domain\Email\EmailId;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class LoadEmailsData.
 */
class LoadEmailsData extends ContainerAwareFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $commandBus = $this->getContainer()->get('broadway.command_handling.command_bus');
        $emailService = $this->getContainer()->get('oloy.email.settings');
        $emails = $emailService->getEmailsParameter();

        foreach ($emails as $email) {
            $commandBus->dispatch(
                new CreateEmail(
                    new EmailId($this->generateUuid()),
                    [
                        'key' => $email['template'],
                        'subject' => $email['subject'],
                        'content' => $email['content'],
                        'senderName' => $this->getContainer()->getParameter('mailer_from_name'),
                        'senderEmail' => $this->getContainer()->getParameter('mailer_from_address'),
                    ]
                )
            );
        }
    }

    /**
     * Generate random UUID.
     *
     * @return string
     */
    protected function generateUuid()
    {
        return $this->getContainer()->get('broadway.uuid.generator')->generate();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 0;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
