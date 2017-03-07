<?php

namespace OpenLoyalty\Infrastructure\Pos\Persistance\Doctrine\Repository;

use Doctrine\ORM\EntityRepository;
use OpenLoyalty\Domain\Pos\Pos;
use OpenLoyalty\Domain\Pos\PosId;
use OpenLoyalty\Domain\Pos\PosRepository;

/**
 * Class DoctrinePosRepository.
 */
class DoctrinePosRepository extends EntityRepository implements PosRepository
{
    public function findAll($returnQueryBuilder = false)
    {
        if ($returnQueryBuilder) {
            return $this->createQueryBuilder('e');
        }

        return parent::findAll();
    }

    public function byId(PosId $posId)
    {
        return parent::find($posId);
    }

    public function save(Pos $pos)
    {
        $this->getEntityManager()->persist($pos);
        $this->getEntityManager()->flush();
    }

    public function remove(Pos $pos)
    {
        $this->getEntityManager()->remove($pos);
    }

    public function oneByIdentifier($identifier)
    {
        return $this->findOneBy(['identifier' => $identifier]);
    }

    public function findAllPaginated($page = 1, $perPage = 10, $sortField = null, $direction = 'ASC')
    {
        $qb = $this->createQueryBuilder('l');
        if ($page < 1) {
            $page = 1;
        }

        if ($sortField) {
            $qb->orderBy('l.'.$sortField, $direction);
        }
        if ($perPage) {
            $qb->setMaxResults($perPage);
            $qb->setFirstResult(($page - 1) * $perPage);
        }

        return $qb->getQuery()->getResult();
    }

    public function countTotal()
    {
        $qb = $this->createQueryBuilder('l');
        $qb->select('count(l.posId)');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
