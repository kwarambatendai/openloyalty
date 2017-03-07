<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Audit\Command;

/**
 * Class CreateAuditLog.
 */
class CreateAuditLog extends AuditLogCommand
{
    /**
     * @var array
     */
    protected $auditLogData;

    /**
     * CreateAuditLog constructor.
     *
     * @param array $auditLogData
     */
    public function __construct(array $auditLogData)
    {
        parent::__construct(null);
        $this->auditLogData = $auditLogData;
    }

    /**
     * @return array
     */
    public function getAuditLogData(): array
    {
        return $this->auditLogData;
    }

    /**
     * @param string $eventType
     * @param string $entityType
     * @param string $entityId
     * @param string $username
     * @param array  $data
     *
     * @return CreateAuditLog
     */
    public static function create($eventType, $entityType, $entityId, $username, $data)
    {
        return new self(
            [
                'eventType' => $eventType,
                'entityType' => $entityType,
                'entityId' => $entityId,
                'data' => $data,
                'username' => $username,
                'createdAt' => new \DateTime(),
            ]
        );
    }
}
