<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Pos;

interface PosRepository
{
    public function byId(PosId $posId);

    public function oneByIdentifier($identifier);

    public function findAll();

    public function save(Pos $pos);

    public function remove(Pos $pos);

    public function findAllPaginated($page = 1, $perPage = 10, $sortField = null, $direction = 'DESC');

    public function countTotal();

    /**
     * @param array      $criteria
     * @param array|null $orderBy
     * @param int|null   $limit
     * @param int|null   $offset
     *
     * @return array the objects
     *
     * @throws \UnexpectedValueException
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);
}
