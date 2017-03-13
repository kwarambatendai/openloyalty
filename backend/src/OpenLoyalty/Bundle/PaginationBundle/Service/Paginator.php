<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\PaginationBundle\Service;

use OpenLoyalty\Bundle\PaginationBundle\Model\Pagination;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Paginator.
 */
class Paginator
{
    protected $pageFieldName;

    protected $perPageFieldName;

    protected $sortFieldName;

    protected $sortDirectionFieldName;

    protected $perPageDefault;

    /**
     * Paginator constructor.
     *
     * @param $pageFieldName
     * @param $perPageFieldName
     * @param $sortFieldName
     * @param $sortDirectionFieldName
     * @param $perPageDefault
     */
    public function __construct(
        $pageFieldName,
        $perPageFieldName,
        $sortFieldName,
        $sortDirectionFieldName,
        $perPageDefault
    ) {
        $this->pageFieldName = $pageFieldName;
        $this->perPageFieldName = $perPageFieldName;
        $this->sortFieldName = $sortFieldName;
        $this->sortDirectionFieldName = $sortDirectionFieldName;
        $this->perPageDefault = $perPageDefault;
    }

    public function handleFromRequest(Request $request, $defaultSortField = null, $defaultSortDirection = 'ASC')
    {
        return new Pagination(
            $request->get($this->pageFieldName, 1),
            $request->get($this->perPageFieldName, $this->perPageDefault),
            $request->get($this->sortFieldName, $defaultSortField),
            $request->get($this->sortDirectionFieldName, $defaultSortDirection)
        );
    }
}
