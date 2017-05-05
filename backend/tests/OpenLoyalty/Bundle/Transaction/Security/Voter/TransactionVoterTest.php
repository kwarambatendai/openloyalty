<?php

namespace OpenLoyalty\Bundle\Transaction\Security\Voter;

use OpenLoyalty\Bundle\BaseVoterTest;
use OpenLoyalty\Bundle\TransactionBundle\DataFixtures\ORM\LoadTransactionData;
use OpenLoyalty\Bundle\TransactionBundle\Security\Voter\TransactionVoter;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Seller\ReadModel\SellerDetailsRepository;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetails;
use OpenLoyalty\Domain\Transaction\TransactionId;

/**
 * Class TransactionVoterTest.
 */
class TransactionVoterTest extends BaseVoterTest
{
    const TRANSACTION_ID = '00000000-0000-474c-b092-b0dd880c0700';
    const TRANSACTION2_ID = '00000000-0000-474c-b092-b0dd880c0701';

    /**
     * @test
     */
    public function it_works()
    {
        $attributes = [
            TransactionVoter::LIST_TRANSACTIONS => ['seller' => false, 'customer' => false, 'admin' => true],
            TransactionVoter::LIST_CURRENT_CUSTOMER_TRANSACTIONS => ['seller' => false, 'customer' => true, 'admin' => false],
            TransactionVoter::LIST_CURRENT_POS_TRANSACTIONS => ['seller' => true, 'customer' => false, 'admin' => false],
            TransactionVoter::VIEW => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => self::TRANSACTION_ID],
            TransactionVoter::CREATE_TRANSACTION => ['seller' => false, 'customer' => false, 'admin' => true],
            TransactionVoter::ASSIGN_CUSTOMER_TO_TRANSACTION => ['seller' => true, 'customer' => false, 'admin' => true, 'id' => LoadTransactionData::TRANSACTION_ID],
            TransactionVoter::LIST_ITEM_LABELS => ['seller' => true, 'customer' => true, 'admin' => true],
        ];
        $repo = $this->getMockBuilder(SellerDetailsRepository::class)->getMock();
        $repo->method('find')->with($this->isType('string'))->willReturn(null);
        $voter = new TransactionVoter($repo);

        $this->makeAssertions($attributes, $voter);

        $attributes = [
            TransactionVoter::VIEW => ['seller' => true, 'customer' => true, 'admin' => true, 'id' => self::TRANSACTION2_ID],
        ];

        $this->makeAssertions($attributes, $voter);
    }

    protected function getSubjectById($id)
    {
        $transaction = $this->getMockBuilder(TransactionDetails::class)->disableOriginalConstructor()->getMock();
        $transaction->method('getTransactionId')->willReturn(new TransactionId($id));
        $customerId = null;
        if ($id == self::TRANSACTION2_ID) {
            $customerId = new CustomerId(self::USER_ID);
        }
        $transaction->method('getCustomerId')->willReturn($customerId);

        return $transaction;
    }
}
