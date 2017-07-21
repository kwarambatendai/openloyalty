<?php

namespace OpenLoyalty\Domain\Audit\Model;

/**
 * Class AuditLogSearchCriteria.
 */
class AuditLogSearchCriteria
{
    private $entityId;
    private $entityType;
    private $eventType;
    private $username;
    private $auditLogId;

    /**
     * @var \DateTime
     */
    private $createdAtFrom;

    /**
     * @var \DateTime
     */
    private $createdAtTo;

    /**
     * AuditLogSearchCriteria constructor.
     *
     * @param $entityId
     * @param $entityType
     * @param $eventType
     * @param $username
     * @param $auditLogId
     * @param \DateTime $createdAtFrom
     * @param \DateTime $createdAtTo
     */
    public function __construct(
        $entityId = null,
        $entityType = null,
        $eventType = null,
        $username = null,
        $auditLogId = null,
        \DateTime $createdAtFrom = null,
        \DateTime $createdAtTo = null
    ) {
        $this->entityId = $entityId;
        $this->entityType = $entityType;
        $this->eventType = $eventType;
        $this->username = $username;
        $this->auditLogId = $auditLogId;
        $this->createdAtFrom = $createdAtFrom;
        $this->createdAtTo = $createdAtTo;
    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * @return mixed
     */
    public function getEventType()
    {
        return $this->eventType;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getAuditLogId()
    {
        return $this->auditLogId;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAtFrom()
    {
        return $this->createdAtFrom;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAtTo()
    {
        return $this->createdAtTo;
    }

    /**
     * @param \DateTime $createdAtFrom
     */
    public function setCreatedAtFrom($createdAtFrom)
    {
        $this->createdAtFrom = $createdAtFrom;
    }

    /**
     * @param \DateTime $createdAtTo
     */
    public function setCreatedAtTo($createdAtTo)
    {
        $this->createdAtTo = $createdAtTo;
    }
}
