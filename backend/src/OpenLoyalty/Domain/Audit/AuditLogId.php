<?php

namespace OpenLoyalty\Domain\Audit;

use Assert\Assertion;
use OpenLoyalty\Domain\Identifier;

/**
 * Class AuditLogId.
 */
class AuditLogId implements Identifier
{
    /**
     * @var string
     */
    private $auditLogId;

    /**
     * AuditLog constructor.
     *
     * @param string $auditLogId
     */
    public function __construct($auditLogId)
    {
        Assertion::string($auditLogId);
        Assertion::uuid($auditLogId);

        $this->auditLogId = $auditLogId;
    }

    public function __toString()
    {
        return $this->auditLogId;
    }
}
