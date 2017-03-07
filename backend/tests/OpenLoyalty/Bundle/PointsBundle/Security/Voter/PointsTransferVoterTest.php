<?php

namespace OpenLoyalty\Bundle\PointsBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;
use OpenLoyalty\Domain\Account\PointsTransferId;
use OpenLoyalty\Domain\Account\ReadModel\PointsTransferDetails;

/**
 * Class PointsTransferVoterTest.
 */
class PointsTransferVoterTest extends BaseVoterTest
{
    const TRANSFER_ID = '00000000-0000-474c-b092-b0dd880c0700';

    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            PointsTransferVoter::LIST_POINTS_TRANSFERS => ['seller' => true, 'customer' => false, 'admin' => true],
            PointsTransferVoter::LIST_CUSTOMER_POINTS_TRANSFERS => ['seller' => false, 'customer' => true, 'admin' => false],
            PointsTransferVoter::ADD_POINTS => ['seller' => false, 'customer' => false, 'admin' => true],
            PointsTransferVoter::SPEND_POINTS => ['seller' => false, 'customer' => false, 'admin' => true],
            PointsTransferVoter::CANCEL => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::TRANSFER_ID],
        ];

        $voter = new PointsTransferVoter();

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        $level = $this->getMockBuilder(PointsTransferDetails::class)->disableOriginalConstructor()->getMock();
        $level->method('getPointsTransferId')->willReturn(new PointsTransferId($id));

        return $level;
    }
}
