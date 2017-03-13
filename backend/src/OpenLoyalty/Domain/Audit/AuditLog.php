<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Audit;

use Assert\Assertion as Assert;

/**
 * Class AuditLog.
 */
class AuditLog
{
    /** @var AuditLogId */
    private $auditLogId;

    /** @var \DateTime */
    private $createdAt;

    /** @var string */
    private $eventType;

    /** @var string */
    private $entityType;

    /** @var string */
    private $entityId;

    /** @var string */
    private $username;

    /** @var array */
    private $data;

    /**
     * AuditLog constructor.
     *
     * @param AuditLogId $auditLogId
     * @param string     $eventType
     * @param string     $entityType
     * @param string     $entityId
     * @param \DateTime  $createdAt
     * @param string     $username
     * @param string     $data
     */
    public function __construct(
        AuditLogId $auditLogId,
        $eventType,
        $entityType,
        $entityId,
        $createdAt,
        $username,
        $data
    ) {
        Assert::notEmpty($auditLogId);
        Assert::notEmpty($entityType);
        Assert::notEmpty($eventType);
        Assert::notEmpty($entityId);
        Assert::notEmpty($createdAt);
        Assert::notEmpty($username);
        Assert::uuid($entityId);

        $this->auditLogId = $auditLogId;
        $this->entityType = $entityType;
        $this->eventType = $eventType;
        $this->entityId = $entityId;
        $this->username = $username;
        $this->data = $data;
        $this->createdAt = $createdAt;
    }

    /**
     * @return AuditLogId
     */
    public function getAuditLogId(): AuditLogId
    {
        return $this->auditLogId;
    }

    /**
     * @param AuditLogId $auditLogId
     */
    public function setAuditLogId(AuditLogId $auditLogId)
    {
        $this->auditLogId = $auditLogId;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getEntityType(): string
    {
        return $this->entityType;
    }

    /**
     * @param string $entityType
     */
    public function setEntityType(string $entityType)
    {
        $this->entityType = $entityType;
    }

    /**
     * @return string
     */
    public function getEntityId(): string
    {
        return $this->entityId;
    }

    /**
     * @param string $entityId
     */
    public function setEntityId(string $entityId)
    {
        $this->entityId = $entityId;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getEventType(): string
    {
        return $this->eventType;
    }

    /**
     * @param string $eventType
     */
    public function setEventType(string $eventType)
    {
        $this->eventType = $eventType;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username)
    {
        $this->username = $username;
    }
}
