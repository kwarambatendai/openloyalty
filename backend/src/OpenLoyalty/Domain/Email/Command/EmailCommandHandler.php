<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 01.02.17 14:37
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace OpenLoyalty\Domain\Email\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventDispatcher\EventDispatcherInterface;
use OpenLoyalty\Domain\Email\Email;
use OpenLoyalty\Domain\Email\EmailRepositoryInterface;
use OpenLoyalty\Domain\Email\SystemEvent\EmailCreatedSystemEvent;
use OpenLoyalty\Domain\Email\SystemEvent\EmailSystemEvents;

/**
 * Class EmailCommandHandler.
 */
class EmailCommandHandler extends CommandHandler
{
    /**
     * Email settings repository.
     *
     * @var EmailRepositoryInterface
     */
    private $repository;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var array
     */
    private $params;

    /**
     * EmailCommandHandler constructor.
     *
     * @param EmailRepositoryInterface $repository
     * @param EventDispatcherInterface $eventDispatcher
     * @param array                    $params
     */
    public function __construct(EmailRepositoryInterface $repository, EventDispatcherInterface $eventDispatcher, array $params)
    {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
        $this->params = $params;
    }

    /**
     * Create email.
     *
     * @param CreateEmail $command
     */
    public function handleCreateEmail(CreateEmail $command)
    {
        $data = $command->getEmailData();
        $email = Email::create($command->getEmailId(), $data);
        $this->repository->save($email);

        $this->eventDispatcher->dispatch(
            EmailSystemEvents::EMAIL_CREATED,
            [new EmailCreatedSystemEvent($command->getEmailId(), $data)]
        );
    }

    /**
     * Update email.
     *
     * @param UpdateEmail $command
     */
    public function handleUpdateEmail(UpdateEmail $command)
    {
        $email = $this->repository->getById($command->getEmailId());
        $data = $command->getEmailData();
        $email->setSubject($this->getData($data, 'subject'));
        $email->setContent($this->getData($data, 'content'));
        $email->setSenderName($this->getData($data, 'senderName', $this->getSenderName()));
        $email->setSenderName($this->getData($data, 'senderEmail', $this->getSenderEmail()));
        $this->repository->save($email);

        $this->eventDispatcher->dispatch(
            EmailSystemEvents::EMAIL_CREATED,
            [new EmailCreatedSystemEvent($command->getEmailId(), null)]
        );
    }

    /**
     * @return string
     */
    protected function getSenderName(): string
    {
        return $this->params['from_name'];
    }

    /**
     * @return string
     */
    protected function getSenderEmail(): string
    {
        return $this->params['from_email'];
    }

    /**
     * Get data.
     *
     * @param      $data
     * @param      $key
     * @param null $default
     *
     * @return null|mixed
     */
    protected function getData($data, $key, $default = null)
    {
        return array_key_exists($key, $data) ? $data[$key] : $default;
    }
}
