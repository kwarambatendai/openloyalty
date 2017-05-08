<?php

namespace OpenLoyalty\Bundle\UtilityBundle\Service;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Account\Account;
use OpenLoyalty\Domain\Campaign\CustomerId;
use OpenLoyalty\Domain\Campaign\SegmentId;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomersBelongingToOneLevel;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelId;
use OpenLoyalty\Domain\Segment\Segment;

/**
 * Class CampaignValidatorTest.
 */
class CustomerDetailsCsvFormatterTest extends \PHPUnit_Framework_TestCase
{
    protected $repo;
    protected $custDetailsRepo;
    protected $levelRepo;
    protected $segment;
    protected $level;

    protected function setUp()
    {
        $this->repo = $this->getMockBuilder(RepositoryInterface::class)->getMock();
        $this->custDetailsRepo = $this->getMockBuilder(RepositoryInterface::class)->getMock();
        $this->levelRepo = $this->getMockBuilder(RepositoryInterface::class)->getMock();

        $customerDetails = $this->getMockBuilder(CustomerDetails::class)->setMockClassName('CustomerDetails')->disableOriginalConstructor()->getMock();
        $customerDetails->method('getBirthDate')->willReturn(new \DateTime());
        $customerDetails->method('getCreatedAt')->willReturn(new \DateTime());

        $customerId = $this->getMockBuilder(CustomerId::class)->disableOriginalConstructor()->getMock();
        $customerId->method('__toString')->willReturn('0000000000');

        $account = $this->getMockBuilder(Account::class)->disableOriginalConstructor()->getMock();
        $account->method('getCustomerId')->willReturn($customerId);

        $customersLevel = $this->getMockBuilder(CustomersBelongingToOneLevel::class)->disableOriginalConstructor()->getMock();
        $customersLevel->method('getCustomers')->willReturn([['customerId' => '0000000']]);
        $levelId = $this->getMockBuilder(LevelId::class)->disableOriginalConstructor()->getMock();
        $levelId->method('__toString')->willReturn('00000000');

        $this->level = $this->getMockBuilder(Level::class)->disableOriginalConstructor()->getMock();
        $this->level->method('getLevelId')->willReturn($levelId);

        $segmentId = $this->getMockBuilder(SegmentId::class)->disableOriginalConstructor()->getMock();
        $segmentId->method('__toString')->willReturn('00000');

        $this->segment = $this->getMockBuilder(Segment::class)->disableOriginalConstructor()->getMock();
        $this->segment->method('getSegmentId')->willReturn($segmentId);

        $this->repo->method('findBy')->with($this->arrayHasKey('segmentId'))
            ->willReturn([$account]);
        $this->levelRepo->method('findBy')->with($this->arrayHasKey('levelId'))
            ->willReturn([$customersLevel]);
        $this->custDetailsRepo->method('find')->willReturn($customerDetails);
    }

    /**
     * @test
     */
    public function it_returns_properly_formatted_csv()
    {
        $formatter = new CustomerDetailsCsvFormatter($this->repo, $this->custDetailsRepo, $this->levelRepo);
        $segment = $formatter->getFormattedSegmentUsers($this->segment);
        $this->assertInternalType('array', $segment);

        $level = $formatter->getFormattedLevelUsers($this->level);
        $this->assertInternalType('array', $level);
    }
}
