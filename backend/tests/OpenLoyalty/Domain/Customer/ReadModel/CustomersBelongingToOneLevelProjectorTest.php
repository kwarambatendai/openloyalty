<?php

namespace OpenLoyalty\Domain\Customer\ReadModel;

use Broadway\ReadModel\InMemory\InMemoryRepository;
use Broadway\ReadModel\Testing\ProjectorScenarioTestCase;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\Event\CustomerWasMovedToLevel;
use OpenLoyalty\Domain\Customer\LevelId;
use OpenLoyalty\Domain\Level\LevelRepository;

/**
 * Class CustomersBelongingToOneLevelProjectorTest.
 */
class CustomersBelongingToOneLevelProjectorTest extends ProjectorScenarioTestCase
{
    /**
     * @var CustomerId
     */
    protected $customerId;

    /**
     * @var CustomerId
     */
    protected $customer2Id;

    /**
     * @var LevelId
     */
    protected $levelId;

    /**
     * @var LevelId
     */
    protected $level2Id;

    protected function createProjector(InMemoryRepository $repository)
    {
        $this->customerId = new CustomerId('00000000-1111-0000-0000-000000000000');
        $this->customer2Id = new CustomerId('00000000-2222-0000-0000-000000000000');
        $this->levelId = new LevelId('00000000-2222-0000-0000-000000000111');
        $this->level2Id = new LevelId('00000000-2222-0000-0000-000000000222');

        $customerRepository = $this->getMockBuilder(InMemoryRepository::class)->getMock();
        $customerData = $this->getCustomerData($this->levelId);
        $customerData['id'] = $this->customerId->__toString();
        $customer = CustomerDetails::deserialize($customerData);
        $customer2Data = $this->getCustomerData($this->levelId, 'Andrzej');
        $customer2Data['id'] = $this->customer2Id->__toString();
        $customer2 = CustomerDetails::deserialize($customer2Data);

        $customerRepository->method('find')
            ->with($this->logicalOr(
                $this->equalTo($this->customerId->__toString()),
                $this->equalTo($this->customer2Id->__toString())
            ))
            ->will($this->returnCallback(function($id) use ($customer, $customer2) {
                if ($id == $customer->getId()) {
                    return $customer;
                } else {
                    return $customer2;
                }
            }));
        $levelRepo = $this->getMockBuilder(LevelRepository::class)->getMock();
        $levelRepo->method('byId')->willReturn(null);

        return new CustomersBelongingToOneLevelProjector($customerRepository, $repository, $levelRepo);
    }

    /**
     * @test
     */
    public function it_add_customer_to_level()
    {
        $this->scenario
            ->given([
            ])
            ->when(new CustomerWasMovedToLevel($this->customerId, $this->levelId))
            ->then(array(
                $this->createBaseReadModel($this->customerId, $this->levelId, $this->getCustomerData($this->levelId)),
            ));
    }

    /**
     * @test
     */
    public function it_changes_customer_level()
    {
        $this->scenario
            ->given([
                new CustomerWasMovedToLevel($this->customerId, $this->levelId),
            ])
            ->when(new CustomerWasMovedToLevel($this->customerId, $this->level2Id))
            ->then(array(
                $this->createBaseReadModel($this->customerId, $this->levelId, null),
                $this->createBaseReadModel($this->customerId, $this->level2Id, $this->getCustomerData($this->levelId)),
            ));
    }

    /**
     * @test
     */
    public function it_add_multiple_customers_to_one_level()
    {
        $this->scenario
            ->given([
                new CustomerWasMovedToLevel($this->customerId, $this->levelId),
            ])
            ->when(new CustomerWasMovedToLevel($this->customer2Id, $this->levelId))
            ->then(array(
                $this->createBaseReadModelWithMultipleCustomers($this->levelId, [
                    [
                        'id' => $this->customerId,
                        'data' => $this->getCustomerData($this->levelId),
                    ],
                    [
                        'id' => $this->customer2Id,
                        'data' => $this->getCustomerData($this->levelId, 'Andrzej'),
                    ],
                ]),
            ));
    }

    private function createBaseReadModel(CustomerId $customerId, LevelId $levelId, array $data = null)
    {
        $obj = new CustomersBelongingToOneLevel($levelId);
        if ($data) {
            $data['id'] = $customerId->__toString();
            $obj->addCustomer(CustomerDetails::deserialize($data));
        }

        return $obj;
    }

    private function createBaseReadModelWithMultipleCustomers(LevelId $levelId, array $customers = [])
    {
        $obj = new CustomersBelongingToOneLevel($levelId);
        foreach ($customers as $customer) {
            $data = $customer['data'];
            $data['id'] = $customer['id']->__toString();
            $obj->addCustomer(CustomerDetails::deserialize($data));
        }

        return $obj;
    }

    private function getCustomerData(LevelId $levelId, $name = 'John')
    {
        return [
            'firstName' => $name,
            'lastName' => 'Doe',
            'levelId' => $levelId->__toString(),
            'gender' => 'male',
            'email' => 'customer@open.com',
            'birthDate' => 653011200,
            'phone' => '123',
            'createdAt' => 1470646394,
            'loyaltyCardNumber' => '000000',
            'company' => [
                'name' => 'test',
                'nip' => 'nip',
            ],
            'address' => [
                'street' => 'Dmowskiego',
                'address1' => '21',
                'city' => 'Wrocław',
                'country' => 'PL',
                'postal' => '50-300',
                'province' => 'Dolnośląskie',
            ],
        ];
    }
}
