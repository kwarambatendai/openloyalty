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
use OpenLoyalty\Domain\Audit\Model\AuditLogSearchCriteria;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
     * @QueryParam(name="entityType", nullable=true, description="Entity Type"))
     * @QueryParam(name="username", nullable=true, description="username"))
     * @QueryParam(name="auditLogId", nullable=true, description="audit log ID"))
     * @QueryParam(name="createdAtFrom", nullable=true, description="created at from"))
     * @QueryParam(name="createdAtTo", nullable=true, description="created at to"))
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getAuditLog(Request $request, ParamFetcher $paramFetcher)
    {
        $pagination = $this->get('oloy.pagination')->handleFromRequest($request, 'createdAt', 'desc');

        /* @var AuditLogRepository $readRepository */
        $auditRepository = $this->get('oloy.audit.log.repository');

        $criteria = $this->createCriteriaFromParamFetcher($paramFetcher);
        $form = $this->get('form.factory')->createNamedBuilder('', FormType::class, null, [
            'allow_extra_fields' => true,
            'method' => 'GET',
        ])
            ->add('createdAtFrom', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'format' => DateTimeType::HTML5_FORMAT,
            ])
            ->add('createdAtTo', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'format' => DateTimeType::HTML5_FORMAT,
            ])->getForm();

        $form->handleRequest($request);

        $criteria->setCreatedAtFrom($form->get('createdAtFrom')->getData());
        $criteria->setCreatedAtTo($form->get('createdAtTo')->getData());

        $logs = $auditRepository->findAllPaginated(
            $criteria,
            $pagination->getPage(),
            $pagination->getPerPage(),
            $pagination->getSort(),
            $pagination->getSortDirection()
        );
        $total = $auditRepository->countTotal(
            $this->createCriteriaFromParamFetcher($paramFetcher)
        );

        return $this->view(
            [
                'logs' => $logs,
                'total' => $total,
            ],
            Response::HTTP_OK
        );
    }

    private function createCriteriaFromParamFetcher(ParamFetcher $paramFetcher)
    {
        return new AuditLogSearchCriteria(
            $paramFetcher->get('entityId', null),
            $paramFetcher->get('entityType', null),
            $paramFetcher->get('eventType', null),
            $paramFetcher->get('username', null),
            $paramFetcher->get('auditLogId', null)
        );
    }
}
