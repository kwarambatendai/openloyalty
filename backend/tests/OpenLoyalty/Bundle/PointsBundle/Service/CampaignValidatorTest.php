<?php

namespace OpenLoyalty\Bundle\PointsBundle\Service;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Bundle\CampaignBundle\Service\CampaignValidator;
use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use OpenLoyalty\Domain\Campaign\Campaign;
use OpenLoyalty\Domain\Campaign\CampaignId;
use OpenLoyalty\Domain\Campaign\CustomerId;
use OpenLoyalty\Domain\Campaign\Model\Coupon;
use OpenLoyalty\Domain\Campaign\ReadModel\CouponUsageRepository;

/**
 * Class CampaignValidatorTest.
 */
class CampaignValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @expectedException \OpenLoyalty\Bundle\CampaignBundle\Exception\NotEnoughPointsException
     */
    public function it_throws_exception_when_there_is_not_enough_points()
    {
        $validator = new CampaignValidator($this->getCouponUsageRepository(0, 0), $this->getAccountDetailsRepository(10));
        $campaign = new Campaign(new CampaignId('00000000-0000-474c-b092-b0dd880c07e1'), ['costInPoints' => 20]);
        $validator->checkIfCustomerHasEnoughPoints($campaign, new CustomerId('00000000-0000-474c-b092-b0dd880c07e1'));
    }

    /**
     * @test
     */
    public function it_not_throwing_exception_when_there_is_enough_points()
    {
        $validator = new CampaignValidator($this->getCouponUsageRepository(0, 0), $this->getAccountDetailsRepository(10));
        $campaign = new Campaign(new CampaignId('00000000-0000-474c-b092-b0dd880c07e1'), ['costInPoints' => 10]);
        $validator->checkIfCustomerHasEnoughPoints($campaign, new CustomerId('00000000-0000-474c-b092-b0dd880c07e1'));
    }

    /**
     * @test
     * @expectedException \OpenLoyalty\Bundle\CampaignBundle\Exception\NoCouponsLeftException
     */
    public function it_throws_exception_when_campaign_is_unlimited_and_there_is_no_coupons_left()
    {
        $campaign = new Campaign(new CampaignId('00000000-0000-474c-b092-b0dd880c07e1'), ['costInPoints' => 10]);
        $campaign->setUnlimited(true);
        $campaign->setCoupons([new Coupon('123'), new Coupon('1234')]);
        $validator = new CampaignValidator($this->getCouponUsageRepository(2, 0), $this->getAccountDetailsRepository(10));
        $validator->validateCampaignLimits($campaign, new CustomerId('00000000-0000-474c-b092-b0dd880c07e1'));
    }

    /**
     * @test
     */
    public function it_not_throwing_exception_when_campaign_is_unlimited_and_there_are_coupons_left()
    {
        $campaign = new Campaign(new CampaignId('00000000-0000-474c-b092-b0dd880c07e1'), ['costInPoints' => 10]);
        $campaign->setUnlimited(true);
        $campaign->setCoupons([new Coupon('123'), new Coupon('1234')]);
        $validator = new CampaignValidator($this->getCouponUsageRepository(1, 0), $this->getAccountDetailsRepository(10));
        $validator->validateCampaignLimits($campaign, new CustomerId('00000000-0000-474c-b092-b0dd880c07e1'));
    }

    /**
     * @test
     * @expectedException \OpenLoyalty\Bundle\CampaignBundle\Exception\CampaignLimitExceededException
     */
    public function it_throws_exception_when_campaign_is_limited_and_limit_is_exceeded()
    {
        $campaign = new Campaign(new CampaignId('00000000-0000-474c-b092-b0dd880c07e1'), ['costInPoints' => 10]);
        $campaign->setUnlimited(false);
        $campaign->setLimit(1);
        $campaign->setCoupons([new Coupon('123'), new Coupon('1234')]);
        $validator = new CampaignValidator($this->getCouponUsageRepository(1, 0), $this->getAccountDetailsRepository(10));
        $validator->validateCampaignLimits($campaign, new CustomerId('00000000-0000-474c-b092-b0dd880c07e1'));
    }

    /**
     * @test
     */
    public function it_not_throwing_exception_when_campaign_is_limited_and_limit_is_not_exceeded()
    {
        $campaign = new Campaign(new CampaignId('00000000-0000-474c-b092-b0dd880c07e1'), ['costInPoints' => 10]);
        $campaign->setUnlimited(false);
        $campaign->setLimit(1);
        $campaign->setLimitPerUser(10);
        $campaign->setCoupons([new Coupon('123'), new Coupon('1234')]);
        $validator = new CampaignValidator($this->getCouponUsageRepository(0, 0), $this->getAccountDetailsRepository(10));
        $validator->validateCampaignLimits($campaign, new CustomerId('00000000-0000-474c-b092-b0dd880c07e1'));
    }

    /**
     * @test
     * @expectedException \OpenLoyalty\Bundle\CampaignBundle\Exception\CampaignLimitPerCustomerExceededException
     */
    public function it_throws_exception_when_campaign_is_limited_and_limit_for_user_is_exceeded()
    {
        $campaign = new Campaign(new CampaignId('00000000-0000-474c-b092-b0dd880c07e1'), ['costInPoints' => 10]);
        $campaign->setUnlimited(false);
        $campaign->setLimit(1);
        $campaign->setLimitPerUser(10);
        $campaign->setCoupons([new Coupon('123'), new Coupon('1234')]);
        $validator = new CampaignValidator($this->getCouponUsageRepository(0, 10), $this->getAccountDetailsRepository(10));
        $validator->validateCampaignLimits($campaign, new CustomerId('00000000-0000-474c-b092-b0dd880c07e1'));
    }

    protected function getCouponUsageRepository($usage, $customerUsage)
    {
        $repo = $this->getMock(CouponUsageRepository::class);
        $repo->method('countUsageForCampaign')->with($this->isInstanceOf(CampaignId::class))
            ->willReturn($usage);
        $repo->method('countUsageForCampaignAndCustomer')->with(
            $this->isInstanceOf(CampaignId::class),
            $this->isInstanceOf(CustomerId::class)
        )->willReturn($customerUsage);

        return $repo;
    }

    protected function getAccountDetailsRepository($points)
    {
        $repo = $this->getMock(RepositoryInterface::class);
        $account = $this->getMockBuilder(AccountDetails::class)->disableOriginalConstructor()->getMock();
        $account->method('getAvailableAmount')->willReturn($points);
        $repo->method('findBy')->with($this->arrayHasKey('customerId'))
            ->willReturn([$account]);

        return $repo;
    }
}
