<?php

namespace OpenLoyalty\Bundle\AnalyticsBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetailsRepository;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Class AnalyticsController.
 */
class AnalyticsController extends FOSRestController
{
    /**
     * @Route(name="oloy.analytics.transactions", path="/admin/analytics/transactions")
     * @Method("GET")
     * @Security("is_granted('VIEW_STATS')")
     * @ApiDoc(
     *     name="transactions statistics",
     *     section="Analytics"
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getTransactionsStats()
    {
        /** @var CustomerDetailsRepository $repo */
        $repo = $this->get('oloy.user.read_model.repository.customer_details');
        $currency = $this->get('ol.settings.manager')->getSettingByKey('currency');

        return $this->view([
            'total' => $repo->sumAllByField('transactionsCount'),
            'amount' => $repo->sumAllByField('transactionsAmount'),
            'amountWithoutDeliveryCosts' => $repo->sumAllByField('transactionsAmountWithoutDeliveryCosts'),
            'currency' => $currency ? $currency->getValue() : 'PLN',
        ]);
    }

    /**
     * @Route(name="oloy.analytics.points", path="/admin/analytics/points")
     * @Method("GET")
     * @Security("is_granted('VIEW_STATS')")
     * @ApiDoc(
     *     name="points statistics",
     *     section="Analytics"
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getPointsStats()
    {
        /** @var PointsTransferDetailsRepository $repo */
        $repo = $this->get('oloy.points.account.repository.points_transfer_details');

        return $this->view([
            'totalSpendingTransfers' => $repo->countTotalSpendingTransfers(),
            'totalPointsSpent' => $repo->getTotalValueOfSpendingTransfers(),
        ]);
    }

    /**
     * @Route(name="oloy.analytics.customers", path="/admin/analytics/customers")
     * @Method("GET")
     * @Security("is_granted('VIEW_STATS')")
     * @ApiDoc(
     *     name="points statistics",
     *     section="Analytics"
     * )
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getCustomersStats()
    {
        /** @var CustomerDetailsRepository $repo */
        $repo = $this->get('oloy.user.read_model.repository.customer_details');

        return $this->view([
            'total' => $repo->countTotal(),
        ]);
    }
}
