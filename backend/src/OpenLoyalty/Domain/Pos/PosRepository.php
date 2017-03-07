<?php

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
}
