<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\PaginationBundle\Model;

/**
 * Class Pagination.
 */
class Pagination
{
    /**
     * @var int
     */
    protected $page;

    /**
     * @var int
     */
    protected $perPage;

    /**
     * @var string
     */
    protected $sort;

    /**
     * @var string
     */
    protected $sortDirection;

    /**
     * Pagination constructor.
     *
     * @param int    $page
     * @param int    $perPage
     * @param string $sort
     * @param string $sortDirection
     */
    public function __construct($page = 1, $perPage = 10, $sort = null, $sortDirection = null)
    {
        if ($page < 1) {
            $page = 1;
        }
        if ($perPage < 0) {
            $perPage = null;
        } elseif ($perPage < 1) {
            $perPage = 10;
        }

        $this->page = $page;
        $this->perPage = $perPage;
        $this->sort = $sort;
        $this->setSortDirection($sortDirection);
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getPerPage()
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * @return string
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param string $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return string
     */
    public function getSortDirection()
    {
        return $this->sortDirection;
    }

    /**
     * @param string $sortDirection
     */
    public function setSortDirection($sortDirection)
    {
        if (strtolower($sortDirection) == 'asc') {
            $sortDirection = 'ASC';
        } else {
            $sortDirection = 'DESC';
        }

        $this->sortDirection = $sortDirection;
    }
}
