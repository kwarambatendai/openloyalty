<?php

namespace OpenLoyalty\Bundle\Transaction\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;
use OpenLoyalty\Bundle\TransactionBundle\DataFixtures\ORM\LoadTransactionData;
use OpenLoyalty\Bundle\TransactionBundle\Security\Voter\TransactionVoter;
use OpenLoyalty\Bundle\UtilityBundle\Security\Voter\UtilityVoter;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetailsRepository;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class TransactionVoterTest.
 */
class UtilityVoterTest extends BaseVoterTest
{
    const TRANSACTION_ID = '00000000-0000-474c-b092-b0dd880c0700';
    const TRANSACTION2_ID = '00000000-0000-474c-b092-b0dd880c0701';

    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            UtilityVoter::GENERATE_SEGMENT_CSV => ['seller' => false, 'customer' => false, 'admin' => true],
        ];
        $voter = new UtilityVoter();

        $this->makeAssertions($attributes, $voter);

    }
    protected function getSubjectById($id)
    {
        return null;
    }
}
