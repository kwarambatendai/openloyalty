<?php

namespace OpenLoyalty\Domain\Customer\Command;

use Broadway\CommandHandling\Testing\CommandHandlerScenarioTestCase;
use Broadway\EventDispatcher\EventDispatcherInterface;
use Broadway\EventHandling\EventBusInterface;
use Broadway\EventStore\EventStoreInterface;
use OpenLoyalty\Domain\Customer\CustomerRepository;
use OpenLoyalty\Domain\Customer\Validator\CustomerUniqueValidator;

/**
 * Class CustomerCommandHandlerTest.
 */
abstract class CustomerCommandHandlerTest extends CommandHandlerScenarioTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function createCommandHandler(EventStoreInterface $eventStore, EventBusInterface $eventBus)
    {
        $eventDispatcher = $this->getMockBuilder(EventDispatcherInterface::class)->getMock();
        $eventDispatcher->method('dispatch')->with($this->isType('string'))->willReturn(true);

        return $this->getCustomerCommandHandler($eventStore, $eventBus, $eventDispatcher);
    }

    public static function getCustomerData()
    {
        return [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'gender' => 'male',
            'email' => 'customer@open.com',
            'birthDate' => 653011200,
            'phone' => '123',
            'createdAt' => 1470646394,
            'loyaltyCardNumber' => '000000',
            'updatedAt' => 1470646394,
            'agreement1' => true,
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

    /**
     * @param EventStoreInterface      $eventStore
     * @param EventBusInterface        $eventBus
     * @param EventDispatcherInterface $eventDispatcher
     *
     * @return \OpenLoyalty\Domain\Customer\Command\CustomerCommandHandler
     */
    protected function getCustomerCommandHandler(EventStoreInterface $eventStore, EventBusInterface $eventBus, EventDispatcherInterface $eventDispatcher)
    {
        $customerDetailsRepository = $this->getMockBuilder('Broadway\ReadModel\RepositoryInterface')->getMock();
        $customerDetailsRepository->method('findBy')->willReturn([]);
        $validator = new CustomerUniqueValidator($customerDetailsRepository);

        return new CustomerCommandHandler(
            new CustomerRepository($eventStore, $eventBus),
            $validator,
            $eventDispatcher
        );
    }
}
