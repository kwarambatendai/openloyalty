<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Domain\Segment\ReadModel;

use Broadway\EventDispatcher\EventDispatcherInterface;
use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Identifier;
use OpenLoyalty\Domain\Segment\CustomerId;
use OpenLoyalty\Domain\Segment\Segment;
use OpenLoyalty\Domain\Segment\SegmentId;
use OpenLoyalty\Domain\Segment\SegmentRepository;
use OpenLoyalty\Domain\Segment\SystemEvent\CustomerAddedToSegmentSystemEvent;
use OpenLoyalty\Domain\Segment\SystemEvent\CustomerRemovedFromSegmentSystemEvent;
use OpenLoyalty\Domain\Segment\SystemEvent\SegmentSystemEvents;
use Psr\Log\LoggerInterface;

/**
 * Class SegmentedCustomersProjector.
 */
class SegmentedCustomersProjector
{
    /**
     * @var SegmentedCustomersRepository
     */
    protected $repository;

    /**
     * @var SegmentRepository
     */
    protected $segmentRepository;

    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * SegmentedCustomersProjector constructor.
     *
     * @param RepositoryInterface       $repository
     * @param SegmentRepository         $segmentRepository
     * @param EventDispatcherInterface  $eventDispatcher
     * @param CustomerDetailsRepository $customerDetailsRepository
     */
    public function __construct(
        RepositoryInterface $repository,
        SegmentRepository $segmentRepository,
        EventDispatcherInterface $eventDispatcher,
        CustomerDetailsRepository $customerDetailsRepository
    ) {
        $this->repository = $repository;
        $this->segmentRepository = $segmentRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->customerDetailsRepository = $customerDetailsRepository;
    }

    public function storeSegmentation(Segment $segment, array $customers, array $currentCustomers = [])
    {
        foreach ($customers as $customer) {
            if (!$customer instanceof CustomerId) {
                $customer = new CustomerId($customer);
            }

            $readModel = $this->getReadModel($segment->getSegmentId(), $customer);
            $readModel->setSegmentName($segment->getName());
            $customerDetails = $this->customerDetailsRepository->find($customer);
            if ($customerDetails instanceof CustomerDetails) {
                $readModel->setFirstName($customerDetails->getFirstName());
                $readModel->setLastName($customerDetails->getLastName());
                $readModel->setEmail($customerDetails->getEmail());
                $readModel->setPhone($customerDetails->getPhone());
            }
            $this->repository->save($readModel);
        }

        $this->dispatchEventsForSegmentation(
            $segment->getSegmentId(),
            $this->getCustomersIdsAsStringFromSegmentation($currentCustomers),
            $this->getCustomersIdsAsString($customers)
        );

        $segment->setCustomersCount(count($customers));
        $this->segmentRepository->save($segment);
    }

    public function removeAll()
    {
        foreach ($this->repository->findAll() as $segmented) {
            $this->repository->remove($segmented->getId());
        }
    }

    public function removeOneSegment($id)
    {
        $segmentedCustomers = $this->repository->findBy(['segmentId' => $id]);

        foreach ($segmentedCustomers as $segmented) {
            $this->repository->remove($segmented->getId());
        }
    }

    protected function dispatchEventsForSegmentation(SegmentId $segmentId, $oldCustomers, $newCustomers)
    {
        $dispatcher = $this->eventDispatcher;
        $old = array_diff($oldCustomers, $newCustomers);
        foreach ($old as $o) {
            if ($this->logger) {
                $this->logger->info('[segmentation] customer: '.$o.' removed from segment '.$segmentId->__toString());
            }
            $dispatcher->dispatch(
                SegmentSystemEvents::CUSTOMER_REMOVED_FROM_SEGMENT,
                [new CustomerRemovedFromSegmentSystemEvent($segmentId, new CustomerId($o))]
            );
        }
        $new = array_diff($newCustomers, $oldCustomers);

        foreach ($new as $n) {
            if ($this->logger) {
                $this->logger->info('[segmentation] customer: '.$n.' added to segment '.$segmentId->__toString());
            }
            $dispatcher->dispatch(
                SegmentSystemEvents::CUSTOMER_ADDED_TO_SEGMENT,
                [new CustomerAddedToSegmentSystemEvent($segmentId, new CustomerId($n))]
            );
        }
    }

    protected function getCustomersIdsAsStringFromSegmentation(array $customers)
    {
        return array_map(function (SegmentedCustomers $segmentedCustomers) {
            return $segmentedCustomers->getCustomerId()->__toString();
        }, $customers);
    }

    protected function getCustomersIdsAsString(array $customers)
    {
        return array_map(function ($customerId) {
            if ($customerId instanceof Identifier) {
                $customerId = $customerId->__toString();
            }

            return $customerId;
        }, $customers);
    }

    /**
     * @param SegmentId  $segmentId
     * @param CustomerId $customerId
     *
     * @return \Broadway\ReadModel\ReadModelInterface|null|SegmentedCustomers
     */
    private function getReadModel(SegmentId $segmentId, CustomerId $customerId)
    {
        $readModel = $this->repository->find($segmentId->__toString().'_'.$customerId->__toString());

        if (null === $readModel) {
            $readModel = new SegmentedCustomers($segmentId, $customerId);
        }

        return $readModel;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }
}
