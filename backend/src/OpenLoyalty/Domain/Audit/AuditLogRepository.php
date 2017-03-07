<?php

namespace OpenLoyalty\Domain\Audit;

/**
 * Interface AuditLogRepository.
 */
interface AuditLogRepository
{
    public function byId(AuditLogId $auditLogId);

    public function findAll();

    public function save(AuditLog $auditLog);

    public function remove(AuditLog $auditLog);

    public function countTotal($eventType, $entityId);

    public function findAllPaginated($eventType, $entityId, $page = 1, $perPage = 10, $sortField = null, $direction = 'ASC');
}
