<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Audit\Command;

use Broadway\CommandHandling\CommandHandler;
use Broadway\EventDispatcher\EventDispatcherInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Domain\Audit\AuditLog;
use OpenLoyalty\Domain\Audit\AuditLogId;
use OpenLoyalty\Domain\Audit\AuditLogRepository;
use OpenLoyalty\Domain\Audit\Event\AuditEvents;
use OpenLoyalty\Domain\Audit\Event\AuditLogWasCreated;
use OpenLoyalty\Domain\Audit\SystemEvent\AuditSystemEvents;
use OpenLoyalty\Domain\Audit\SystemEvent\CreatedAuditLogSystemEvent;

/**
 * Class AuditLogHandler.
 */
class AuditLogHandler extends CommandHandler
{
    /**
     * @var AuditLogRepository
     */
    protected $auditLogRepository;

    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * AuditLogHandler constructor.
     *
     * @param AuditLogRepository       $auditLogRepository
     * @param UuidGeneratorInterface   $uuidGenerator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        AuditLogRepository $auditLogRepository,
        UuidGeneratorInterface $uuidGenerator,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->auditLogRepository = $auditLogRepository;
        $this->uuidGenerator = $uuidGenerator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param CreateAuditLog $command
     */
    public function handleCreateAuditLog(CreateAuditLog $command)
    {
        $auditData = $command->getAuditLogData();

        /** @var AuditLog $auditLog */
        $auditLog = new AuditLog(
            new AuditLogId($this->uuidGenerator->generate()),
            $auditData['eventType'],
            $auditData['entityType'],
            $auditData['entityId'],
            $auditData['createdAt'],
            $auditData['username'],
            $auditData['data']
        );
        $this->auditLogRepository->save($auditLog);

        $this->eventDispatcher->dispatch(
            AuditEvents::AUDIT_LOG_CREATED,
            [new AuditLogWasCreated($auditLog->getAuditLogId(), $auditData)]
        );

        $this->eventDispatcher->dispatch(
            AuditSystemEvents::AUDIT_LOG_CREATED,
            [new CreatedAuditLogSystemEvent($auditLog->getAuditLogId())]
        );
    }
}
