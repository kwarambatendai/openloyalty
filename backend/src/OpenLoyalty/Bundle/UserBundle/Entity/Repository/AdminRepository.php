<?php

namespace OpenLoyalty\Bundle\UserBundle\Entity\Repository;

use OpenLoyalty\Bundle\UserBundle\Entity\Admin;

interface AdminRepository
{
    /**
     * @param int    $page
     * @param int    $perPage
     * @param null   $sortField
     * @param string $direction
     *
     * @return Admin[]
     */
    public function findAllPaginated($page = 1, $perPage = 10, $sortField = null, $direction = 'ASC');

    public function countTotal();

    /**
     * @param $email
     * @param $excludedId
     *
     * @return bool
     */
    public function isEmailExist($email, $excludedId = null);
}
