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
use OpenLoyalty\Domain\Audit\Model\AuditLogSearchCriteria;

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
     * @param AuditLogSearchCriteria $criteria
     *
     * @return int
     */
    public function countTotal(AuditLogSearchCriteria $criteria)
    {
        $query = $this->createQueryBuilder('a');
        $query->select('count(a.auditLogId)');

        $this->decorateParams($query, $criteria);

        return $query->getQuery()->getSingleScalarResult();
    }

    /**
     * @param AuditLogSearchCriteria $criteria
     * @param int                    $page
     * @param int                    $perPage
     * @param null                   $sortField
     * @param string                 $direction
     *
     * @return array
     */
    public function findAllPaginated(
        AuditLogSearchCriteria $criteria,
        $page = 1,
        $perPage = 10,
        $sortField = null,
        $direction = 'ASC'
    ) {
        $qb = $this->createQueryBuilder('a');

        $this->decorateParams($qb, $criteria);

        $qb->setMaxResults($perPage);
        $qb->setFirstResult(($page - 1) * $perPage);

        return $qb->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder           $query
     * @param AuditLogSearchCriteria $criteria
     *
     * @return QueryBuilder
     */
    protected function decorateParams(QueryBuilder $query, AuditLogSearchCriteria $criteria)
    {
        if ($criteria->getEventType()) {
            $query->andWhere('a.eventType = :eventType')->setParameter('eventType', $criteria->getEventType());
        }
        if ($criteria->getEntityId()) {
            $query->andWhere('a.entityId = :entityId')->setParameter('entityId', $criteria->getEntityId());
        }
        if ($criteria->getEntityType()) {
            $query->andWhere('a.entityType = :entityType')->setParameter('entityType', $criteria->getEntityType());
        }
        if ($criteria->getAuditLogId()) {
            $query->andWhere('a.auditLogId = :auditLogId')->setParameter('auditLogId', $criteria->getAuditLogId());
        }
        if ($criteria->getUsername()) {
            $query->andWhere('a.username = :username')->setParameter('username', $criteria->getUsername());
        }
        if ($criteria->getCreatedAtFrom()) {
            $query->andWhere('a.createdAt >= :createdAtFrom')->setParameter('createdAtFrom', $criteria->getCreatedAtFrom());
        }
        if ($criteria->getCreatedAtTo()) {
            $query->andWhere('a.createdAt <= :createdAtTo')->setParameter('createdAtTo', $criteria->getCreatedAtTo());
        }

        return $query;
    }
}
