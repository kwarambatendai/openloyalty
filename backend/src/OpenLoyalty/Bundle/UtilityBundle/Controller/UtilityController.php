<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UtilityBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Bundle\UtilityBundle\Service\CustomerDetailsCsvFormatter;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Segment\Segment;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Class UtilityController.
 */
class UtilityController extends FOSRestController
{
    /**
     * Method will return csv with customers assigned to specific segment.
     *
     * @Route(name="oloy.csv.segment.generate", path="/csv/segment/{segment}")
     * @Method("GET")
     * @Security("is_granted('GENERATE_SEGMENT_CSV')")
     * @ApiDoc(
     *     section="Utility"
     * )
     *
     * @param Request $request
     * @param Segment $segment
     *
     * @return Response
     */
    public function generateSegmentCsvAction(Request $request, Segment $segment)
    {
        /** @var CustomerDetailsCsvFormatter $formatter */
        $formatter = $this->get('oloy.utility.customer.details.csv.formatter');
        $customerDetails = $formatter->getFormattedSegmentUsers($segment);
        /** @var StreamedResponse $response */
        $response = $this->createStream($this->getCsvMap(), $customerDetails, $segment->getName());

        return $response;
    }

    /**
     * Method will return csv with customers assigned to specific level.
     *
     * @Route(name="oloy.csv.level.generate", path="/csv/level/{level}")
     * @Method("GET")
     * @Security("is_granted('GENERATE_SEGMENT_CSV')")
     * @ApiDoc(
     *     section="Utility"
     * )
     *
     * @param Request $request
     * @param Level   $level
     *
     * @return Response
     */
    public function generateLevelCsvAction(Request $request, Level $level)
    {
        /** @var CustomerDetailsCsvFormatter $formatter */
        $formatter = $this->get('oloy.utility.customer.details.csv.formatter');
        $customerDetails = $formatter->getFormattedLevelUsers($level);
        /** @var StreamedResponse $response */
        $response = $this->createStream($this->getCsvMap(), $customerDetails, $level->getName());

        return $response;
    }

    /**
     * @return array
     */
    protected function getCsvMap()
    {
        return [
            'First name',
            'Last name',
            'E-mail address',
            'Gender',
            'Telephone',
            'Loyalty card number',
            'Birthdate',
            'Created at',
            'Legal agreement',
            'Marketing agreement',
            'Data processing agreement',
        ];
    }

    /**
     * @param array           $map
     * @param CustomerDetails $customerDetails
     * @param string          $baseFilename
     *
     * @return StreamedResponse
     */
    protected function createStream($map, $customerDetails, $baseFilename)
    {
        /**@var StreamedResponse $response * */
        $response = new StreamedResponse();
        $response->setCallback(function () use ($map, $customerDetails) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $map, ',');
            foreach ($customerDetails as $cd) {
                fputcsv($handle, $cd, ',');
            }
        });

        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $date = new \DateTime('now');
        $filename = str_replace(' ', '-', $baseFilename).'-'.$date->format('Y-m-d-H-i-s');
        $response->headers->set('Content-Disposition', 'attachment; filename="'.$filename.'.csv"');

        return $response;
    }
}
