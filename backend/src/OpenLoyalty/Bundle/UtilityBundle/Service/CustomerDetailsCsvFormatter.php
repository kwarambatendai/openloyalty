<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UtilityBundle\Service;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Segment\Segment;

/**
 * Class EmailProvider.
 */
class CustomerDetailsCsvFormatter
{
    /**
     * @var RepositoryInterface
     */
    private $segmentedCustomersRepository;
    /**
     * @var RepositoryInterface
     */
    private $customerDetailsRepository;
    /**
     * @var RepositoryInterface
     */
    private $levelCustomersRepository;

    /**
     * CustomerDetailsCsvFormatter constructor.
     *
     * @param RepositoryInterface $segmentedCustomersRepository
     * @param RepositoryInterface $customerDetailsRepository
     * @param RepositoryInterface $levelCustomersRepository
     */
    public function __construct(RepositoryInterface $segmentedCustomersRepository, RepositoryInterface $customerDetailsRepository, RepositoryInterface $levelCustomersRepository)
    {
        $this->segmentedCustomersRepository = $segmentedCustomersRepository;
        $this->customerDetailsRepository = $customerDetailsRepository;
        $this->levelCustomersRepository = $levelCustomersRepository;
    }

    /**
     * @param Segment $segment
     *
     * @return array
     */
    public function getFormattedSegmentUsers($segment)
    {
        /** @var array $customers */
        $customers = $this->segmentedCustomersRepository->findBy(['segmentId' => $segment->getSegmentId()->__toString()]);
        $customerDetails = [];
        /** @var CustomerDetails $customer */
        foreach ($customers as $customer) {
            $details = $this->customerDetailsRepository->find($customer->getCustomerId()->__toString());
            if ($details instanceof CustomerDetails) {
                $customerDetails[$customer->getCustomerId()->__toString()] = $this->serializeCustomerDataForCsv($details);
            }
        }

        return $customerDetails;
    }

    /**
     * @param Level $level
     *
     * @return array
     */
    public function getFormattedLevelUsers($level)
    {
        $customers = $this->levelCustomersRepository->findBy(['levelId' => $level->getLevelId()->__toString()]);
        $customerDetails = [];
        /* @var CustomerDetails $cust */
        if (!$customers) {
            return $customerDetails;
        }
        foreach (reset($customers)->getCustomers() as $cust) {
            $details = $this->customerDetailsRepository->find($cust['customerId']);
            if ($details instanceof CustomerDetails) {
                $customerDetails[$cust['customerId']] = $this->serializeCustomerDataForCsv($details);
            }
        }

        return $customerDetails;
    }

    /**
     * @param CustomerDetails $details
     *
     * @return array
     */
    protected function serializeCustomerDataForCsv(CustomerDetails $details)
    {
        $birthdate = '';
        if ($details->getBirthDate()) {
            $birthdate = $details->getBirthDate()->format('Y-m-d H:i:s');
        }

        return [
            $details->getFirstName(),
            $details->getLastName(),
            $details->getEmail(),
            $details->getGender(),
            $details->getPhone(),
            $details->getLoyaltyCardNumber(),
            $birthdate,
            $details->getCreatedAt()->format('Y-m-d H:i:s'),
            $details->isAgreement1(),
            $details->isAgreement2(),
            $details->isAgreement3(),
        ];
    }
}
