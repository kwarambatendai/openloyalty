<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Customer;

use Broadway\EventHandling\EventBusInterface;
use Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStoreInterface;

/**
 * Class CustomerRepository.
 */
class CustomerRepository extends EventSourcingRepository
{
    public function __construct(
        EventStoreInterface $eventStore,
        EventBusInterface $eventBus,
        array $eventStreamDecorators = array()
    ) {
        parent::__construct(
            $eventStore,
            $eventBus,
            '\OpenLoyalty\Domain\Customer\Customer',
            new PublicConstructorAggregateFactory(),
            $eventStreamDecorators
        );
    }
}
