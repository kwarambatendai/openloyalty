<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\AuditBundle\Controller\Api;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Domain\Audit\AuditLogRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AuditController.
 */
class AuditController extends FOSRestController
{
    /**
     * Get audit log.
     *
     * Method will return actions log.
     *
     *
     * @Route(name="oloy.audit.log.get", path="/audit/log")
     * @Method("GET")
     * @Security("is_granted('AUDIT_LOG')")
     *
     * @ApiDoc(
     *     name="Get audit log",
     *     section="Audit",
     *     parameters={
     *      {"name"="eventType", "dataType"="string", "required"=false, "description"="Filter by event type"},
     *      {"name"="entityId", "dataType"="string", "required"=false, "description"="Filter by entity id"},
     *      {"name"="page", "dataType"="integer", "required"=false, "description"="Page number"},
     *      {"name"="perPage", "dataType"="integer", "required"=false, "description"="Number of elements per page"},
     *      {"name"="sort", "dataType"="string", "required"=false, "description"="Field to sort by"},
     *      {"name"="direction", "dataType"="asc|desc", "required"=false, "description"="Sorting direction"},
     *     }
     * )
     *
     * @param Request      $request
     * @param ParamFetcher $paramFetcher
     *
     * @QueryParam(name="eventType", nullable=true, description="Event type"))
     * @QueryParam(name="entityId", nullable=true, description="Entity ID"))
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getAuditLog(Request $request, ParamFetcher $paramFetcher)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request, 'createdAt', 'desc');

        /* @var AuditLogRepository $readRepository */
        $auditRepository = $this->get('oloy.audit.log.repository');

        $logs = $auditRepository->findAllPaginated(
            $paramFetcher->get('eventType'),
            $paramFetcher->get('entityId'),
            $pagination->getPage(),
            $pagination->getPerPage(),
            $pagination->getSort(),
            $pagination->getSortDirection()
        );
        $total = $auditRepository->countTotal(
            $paramFetcher->get('eventType'),
            $paramFetcher->get('entityId')
        );

        return $this->view(
            [
                'logs' => $logs,
                'total' => $total,
            ],
            Response::HTTP_OK
        );
    }
}
