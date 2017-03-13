<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Audit\SystemEvent;

use OpenLoyalty\Domain\Audit\AuditLogId;

/**
 * Class AuditLogSystemEvent.
 */
abstract class AuditLogSystemEvent
{
    /**
     * @var AuditLogId
     */
    protected $auditLogId;

    /**
     * AuditLogSystemEvent constructor.
     *
     * @param AuditLogId $auditLogId
     */
    public function __construct(AuditLogId $auditLogId)
    {
        $this->auditLogId = $auditLogId;
    }

    /**
     * @return AuditLogId
     */
    public function getAuditLogId(): AuditLogId
    {
        return $this->auditLogId;
    }
}
