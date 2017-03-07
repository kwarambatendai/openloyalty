<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyaltyPlugin\SalesManagoBundle\DataProvider;

use OpenLoyalty\Bundle\UserBundle\Status\CustomerStatusProvider;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyaltyPlugin\SalesManagoBundle\Model\Contact;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Level\LevelId;
use OpenLoyalty\Domain\Level\LevelRepository;
use OpenLoyaltyPlugin\SalesManagoBundle\Service\SalesManagoCustomerTranslator;

/**
 * Class ContactDataProvider.
 */
class ContactDataProvider implements DataProviderInterface
{
    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;
    /**
     * @var LevelRepository
     */
    protected $levelRepository;
    /**
     * @var SalesManagoCustomerTranslator
     */
    protected $salesManagoTranslator;

    /**
     * @var CustomerStatusProvider
     */
    protected $customerStatusProvider;

    /**
     * SalesManagoContactUpdateSender constructor.
     *
     * @param LevelRepository               $levelRepository
     * @param CustomerDetailsRepository     $customerDetailsRepository
     * @param SalesManagoCustomerTranslator $salesManagoTranslator
     * @param CustomerStatusProvider        $customerStatusProvider
     */
    public function __construct(
        LevelRepository $levelRepository,
        CustomerDetailsRepository $customerDetailsRepository,
        SalesManagoCustomerTranslator $salesManagoTranslator,
        CustomerStatusProvider $customerStatusProvider
    ) {
        $this->levelRepository = $levelRepository;
        $this->customerDetailsRepository = $customerDetailsRepository;
        $this->salesManagoTranslator = $salesManagoTranslator;
        $this->customerStatusProvider = $customerStatusProvider;
    }

    /**
     * @param CustomerId $customerId
     *
     * @return array
     */
    public function provideData($customerId)
    {
        /** @var CustomerDetails $customer */
        $customer = $this->customerDetailsRepository->find($customerId);

        $customerData = $customer->serialize();
        if (array_key_exists('levelId', $customerData) && $customerData['levelId'] !== null) {
            $additionalData = $this->createCustomerDetails($customerData['levelId'], $customerId);
            $customerData = array_merge($customerData, $additionalData);
        }
        $data = $this->getContact($customerData);

        return $data;
    }

    /**
     * @param $customerId
     *
     * @return mixed
     */
    public function provideInitalData($customerId)
    {
        /** @var CustomerDetails $customer */
        $customer = $this->customerDetailsRepository->find($customerId);

        $customerData = $customer->serialize();
        $contact = new Contact($customerData);
        $data = $contact->toSalesManagoArray();
        $tags = [];
        if ($data['properties']['agreement1'] === true) {
            $tags[] = 'OL-LP-SIGNEDIN';
        } else {
            $tags[] = 'OL-LP-SIGNEDOUT';
        }

        if ($data['properties']['agreement2'] === true) {
            $tags[] = 'OL-NSL-SUBSCRIBE';
        } else {
            $tags[] = 'OL-NSL-UNSUBSCRIBE';
        }
        $data['tags'] = $tags;

        return $this->salesManagoTranslator->translateToSalesManago($data);
    }

    /**
     * @param string     $levelId
     * @param CustomerId $customerId
     *
     * @return array
     */
    public function createCustomerDetails($levelId, $customerId)
    {
        $level = $this->levelRepository->byId(
            new LevelId($levelId)
        );
        $additionalData['levelId'] = $level->getName();
        $additionalData['levelDiscount'] = round((float) $level->getReward()->getValue() * 100).'%';
        $details = $this->customerStatusProvider->getStatus($customerId);
        if ($details->getTransactionsAmountToNextLevel() !== null) {
            $additionalData['transactionsAmountToNextLevel'] = $details->getTransactionsAmountToNextLevel();
        } else {
            $additionalData['transactionsAmountToNextLevel'] = 0;
        }
        $additionalData['clv'] = $details->getTransactionsAmount();
        $additionalData['avo'] = $details->getAverageTransactionsAmount();
        $additionalData['customer_orders'] = $details->getTransactionsCount();

        return $additionalData;
    }

    /**
     * @param $customerData
     *
     * @return array
     */
    protected function getContact($customerData)
    {
        $contact = new Contact($customerData);
        $data = $this->salesManagoTranslator->translateToSalesManago(
            $contact->toSalesManagoArray()
        );

        return $data;
    }
}
