<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Audit\Event;

use OpenLoyalty\Domain\Audit\AuditLogId;

/**
 * Class AuditLogWasCreated.
 */
class AuditLogWasCreated extends AuditLogEvent
{
    /**
     * @var array
     */
    protected $auditLogData;

    /**
     * AuditLogWasCreated constructor.
     *
     * @param AuditLogId $auditLogId
     * @param array      $auditLogData ;
     */
    public function __construct(AuditLogId $auditLogId, array $auditLogData)
    {
        parent::__construct($auditLogId);

        $this->auditLogData = $auditLogData;
    }

    /**
     * @return array
     */
    public function getAuditLogData(): array
    {
        return $this->auditLogData;
    }
}
