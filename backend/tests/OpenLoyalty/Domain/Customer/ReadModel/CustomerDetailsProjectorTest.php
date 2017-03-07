<?php

namespace OpenLoyalty\Domain\Customer\ReadModel;

use Broadway\ReadModel\InMemory\InMemoryRepository;
use Broadway\ReadModel\Testing\ProjectorScenarioTestCase;
use OpenLoyalty\Domain\Customer\Command\CustomerCommandHandlerTest;
use OpenLoyalty\Domain\Customer\Event\CustomerAddressWasUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerCompanyDetailsWereUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerDetailsWereUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerLoyaltyCardNumberWasUpdated;
use OpenLoyalty\Domain\Customer\Event\CustomerWasDeactivated;
use OpenLoyalty\Domain\Customer\Event\CustomerWasRegistered;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Transaction\ReadModel\TransactionDetailsRepository;

/**
 * Class CustomerDetailsProjectorTest.
 */
class CustomerDetailsProjectorTest extends ProjectorScenarioTestCase
{
    /**
     * {@inheritDoc}
     */
    protected function createProjector(InMemoryRepository $repository)
    {
        $transactionDetailsRepo = $this->getMock(TransactionDetailsRepository::class);

        return new CustomerDetailsProjector($repository, $transactionDetailsRepo);
    }

    /**
     * @test
     */
    public function it_creates_a_read_model_on_register()
    {
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000000');

        $this->scenario->given(array())
            ->when(new CustomerWasRegistered($customerId, CustomerCommandHandlerTest::getCustomerData()))
            ->then(array(
                $this->createBaseReadModel($customerId, CustomerCommandHandlerTest::getCustomerData()),
            ));
    }

    /**
     * @test
     */
    public function it_creates_a_read_model_on_register_and_properly_sets_agreements()
    {
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000000');

        $data = CustomerCommandHandlerTest::getCustomerData();
        $data['agreement1'] = true;
        $data['agreement2'] = false;
        $data['agreement3'] = true;

        $this->scenario->given(array())
            ->when(new CustomerWasRegistered($customerId, $data))
            ->then(array(
                $this->createBaseReadModel($customerId, $data),
            ));
    }

    /**
     * @test
     */
    public function it_creates_a_read_model_on_register_and_address_update()
    {
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000000');

        $customerLoyaltyCardNumberWasUpdated = new CustomerLoyaltyCardNumberWasUpdated($customerId,
            CustomerCommandHandlerTest::getCustomerData()['loyaltyCardNumber']);
        $data = CustomerCommandHandlerTest::getCustomerData();
        $data['updatedAt'] = $customerLoyaltyCardNumberWasUpdated->getUpdateAt()->getTimestamp();

        $this->scenario->given(array())
            ->when(new CustomerWasRegistered($customerId, CustomerCommandHandlerTest::getCustomerData()))
            ->when(new CustomerAddressWasUpdated($customerId, CustomerCommandHandlerTest::getCustomerData()['address']))
            ->when(new CustomerCompanyDetailsWereUpdated($customerId, CustomerCommandHandlerTest::getCustomerData()['company']))
            ->when($customerLoyaltyCardNumberWasUpdated)
            ->then(array(
                $this->createReadModel($customerId, $data),
            ));
    }

    /**
     * @test
     */
    public function it_creates_a_read_model_on_register_and_deactivate()
    {
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000000');

        $data = CustomerCommandHandlerTest::getCustomerData();
        $data['active'] = false;
        $data['address'] = null;
        $data['loyaltyCardNumber'] = null;
        $data['company'] = null;

        $this->scenario->given(array())
            ->when(new CustomerWasRegistered($customerId, CustomerCommandHandlerTest::getCustomerData()))
            ->when(new CustomerWasDeactivated($customerId))
            ->then(array(
                $this->createReadModel($customerId, $data),
            ));
    }

    /**
     * @test
     */
    public function it_creates_a_read_model_on_register_and_name_update()
    {
        $customerId = new CustomerId('00000000-0000-0000-0000-000000000000');

        $data = CustomerCommandHandlerTest::getCustomerData();
        $data['firstName'] = 'Jane';
        unset($data['company']);
        unset($data['loyaltyCardNumber']);
        $customerDetailsWereUpdated = new CustomerDetailsWereUpdated($customerId, ['firstName' => 'Jane']);
        $data['updatedAt'] = $customerDetailsWereUpdated->getUpdateAt()->getTimestamp();

        $this->scenario->given(array())
            ->when(new CustomerWasRegistered($customerId, CustomerCommandHandlerTest::getCustomerData()))
            ->when(new CustomerAddressWasUpdated($customerId, CustomerCommandHandlerTest::getCustomerData()['address']))
            ->when($customerDetailsWereUpdated)
            ->then(array(
                $this->createReadModel($customerId, $data),
            ));
    }

    private function createBaseReadModel(CustomerId $customerId, array $data)
    {
        $data['id'] = $customerId->__toString();
        unset($data['loyaltyCardNumber']);
        unset($data['company']);
        unset($data['address']);
        return CustomerDetails::deserialize($data);
    }

    private function createReadModel(CustomerId $customerId, array $data)
    {
        $data['id'] = $customerId->__toString();
        return CustomerDetails::deserialize($data);
    }
}
