<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Audit\Persistance\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use OpenLoyalty\Domain\Audit\AuditLog;
use OpenLoyalty\Domain\Audit\AuditLogId;
use OpenLoyalty\Domain\Audit\AuditLogRepository;

/**
 * Class DoctrineAuditRepository.
 */
class DoctrineAuditLogRepository extends EntityRepository implements AuditLogRepository
{
    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return parent::findAll();
    }

    /**
     * @param AuditLogId $auditLogId
     *
     * @return AuditLog
     */
    public function byId(AuditLogId $auditLogId)
    {
        return parent::find($auditLogId);
    }

    /**
     * @param AuditLog $auditLog
     */
    public function save(AuditLog $auditLog)
    {
        $this->getEntityManager()->persist($auditLog);
        $this->getEntityManager()->flush();
    }

    /**
     * @param AuditLog $auditLog
     */
    public function remove(AuditLog $auditLog)
    {
        $this->getEntityManager()->remove($auditLog);
    }

    /**
     * @param string $eventType
     * @param int    $entityId
     *
     * @return int
     */
    public function countTotal($eventType, $entityId)
    {
        $query = $this->createQueryBuilder('a');
        $query->select('count(a.auditLogId)');

        $this->decorateParams($query, $eventType, $entityId);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param string $eventType
     * @param int    $entityId
     * @param int    $page
     * @param int    $perPage
     * @param null   $sortField
     * @param string $direction
     *
     * @return array
     */
    public function findAllPaginated(
        $eventType,
        $entityId,
        $page = 1,
        $perPage = 10,
        $sortField = null,
        $direction = 'ASC'
    ) {
        $qb = $this->createQueryBuilder('a');

        $this->decorateParams($qb, $eventType, $entityId);

        $qb->setMaxResults($perPage);
        $qb->setFirstResult(($page - 1) * $perPage);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $query
     * @param string       $eventType
     * @param int          $entityId
     *
     * @return QueryBuilder
     */
    protected function decorateParams(QueryBuilder $query, $eventType, $entityId)
    {
        if ($eventType) {
            $query->andWhere('a.eventType = :eventType')->setParameter('eventType', $eventType);
        }
        if ($entityId) {
            $query->andWhere('a.entityId = :entityId')->setParameter('entityId', $entityId);
        }

        return $query;
    }
}
