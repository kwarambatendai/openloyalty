<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\TransactionBundle\Service;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Bundle\SettingsBundle\Service\SettingsManager;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Transaction\CustomerIdProvider;

/**
 * Class SettingsBasedCustomerIdProvider.
 */
class SettingsBasedCustomerIdProvider implements CustomerIdProvider
{
    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * @var RepositoryInterface
     */
    protected $customerDetailsRepository;

    /**
     * SettingsBasedCustomerIdProvider constructor.
     *
     * @param SettingsManager     $settingsManager
     * @param RepositoryInterface $customerDetailsRepository
     */
    public function __construct(SettingsManager $settingsManager, RepositoryInterface $customerDetailsRepository)
    {
        $this->settingsManager = $settingsManager;
        $this->customerDetailsRepository = $customerDetailsRepository;
    }

    /**
     * @param array $customerData
     *
     * @return string|null
     */
    public function getId(array $customerData)
    {
        $priority = $this->settingsManager->getSettingByKey('customersIdentificationPriority');
        if (!$priority) {
            $priority = [
                ['field' => 'loyaltyCardNumber'],
                ['field' => 'email'],
            ];
        } else {
            $priority = $priority->getValue();
        }

        if (count($priority) == 0) {
            return;
        }

        foreach ($priority as $field) {
            if (!isset($customerData[$field['field']])) {
                continue;
            }
            $customers = $this->customerDetailsRepository->findBy(
                [
                    $field['field'] => $customerData[$field['field']],
                ]
            );
            if (count($customers) == 0) {
                continue;
            }
            $customer = reset($customers);

            if ($customer instanceof CustomerDetails && $customer->isActive()) {
                return $customer->getId();
            }
        }

        return;
    }
}
