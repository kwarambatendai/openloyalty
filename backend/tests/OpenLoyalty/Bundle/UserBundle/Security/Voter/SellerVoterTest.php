<?php

namespace OpenLoyalty\Bundle\UserBundle\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetails;
use OpenLoyalty\Domain\Seller\SellerId;

/**
 * Class SellerVoterTest.
 */
class SellerVoterTest extends BaseVoterTest
{
    const SELLER_ID = '00000000-0000-474c-b092-b0dd880c0700';
    const SELLER2_ID = '00000000-0000-474c-b092-b0dd880c0701';

    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            SellerVoter::CREATE_SELLER => ['seller' => false, 'customer' => false, 'admin' => true],
            SellerVoter::LIST_SELLERS => ['seller' => false, 'customer' => false, 'admin' => true],
            SellerVoter::VIEW => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::SELLER_ID],
            SellerVoter::EDIT => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::SELLER_ID],
            SellerVoter::ACTIVATE => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::SELLER_ID],
            SellerVoter::DELETE => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::SELLER_ID],
            SellerVoter::DEACTIVATE => ['seller' => false, 'customer' => false, 'admin' => true, 'id' => self::SELLER_ID],
        ];

        $voter = new SellerVoter();

        $this->makeAssertions($attributes, $voter);

        $attributes = [
            SellerVoter::VIEW => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => self::USER_ID],
        ];

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        $seller = $this->getMockBuilder(SellerDetails::class)->disableOriginalConstructor()->getMock();
        $seller->method('getSellerId')->willReturn(new SellerId($id));

        return $seller;
    }
}
