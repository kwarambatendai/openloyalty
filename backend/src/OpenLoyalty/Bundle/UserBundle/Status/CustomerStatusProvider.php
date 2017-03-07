<?php

namespace OpenLoyalty\Bundle\UserBundle\Status;

use Broadway\ReadModel\RepositoryInterface;
use OpenLoyalty\Bundle\SettingsBundle\Service\SettingsManager;
use OpenLoyalty\Bundle\UserBundle\Model\CustomerStatus;
use OpenLoyalty\Domain\Account\ReadModel\AccountDetails;
use OpenLoyalty\Domain\Customer\CustomerId;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetails;
use OpenLoyalty\Domain\Customer\ReadModel\CustomerDetailsRepository;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelRepository;
use OpenLoyalty\Infrastructure\Customer\ExcludeDeliveryCostsProvider;
use OpenLoyalty\Infrastructure\Customer\TierAssignTypeProvider;

/**
 * Class CustomerStatusProvider.
 */
class CustomerStatusProvider
{
    /**
     * @var RepositoryInterface
     */
    protected $accountDetailsRepository;

    /**
     * @var LevelRepository
     */
    protected $levelRepository;

    /**
     * @var CustomerDetailsRepository
     */
    protected $customerDetailsRepository;

    /**
     * @var TierAssignTypeProvider
     */
    protected $tierAssignTypeProvider;

    /**
     * @var ExcludeDeliveryCostsProvider
     */
    protected $excludeDeliveryCostProvider;

    /**
     * @var SettingsManager
     */
    protected $settingsManager;

    /**
     * CustomerStatusProvider constructor.
     *
     * @param RepositoryInterface          $accountDetailsRepository
     * @param LevelRepository              $levelRepository
     * @param CustomerDetailsRepository    $customerDetailsRepository
     * @param TierAssignTypeProvider       $tierAssignTypeProvider
     * @param ExcludeDeliveryCostsProvider $excludeDeliveryCostProvider
     * @param SettingsManager              $settingsManager
     */
    public function __construct(
        RepositoryInterface $accountDetailsRepository,
        LevelRepository $levelRepository,
        CustomerDetailsRepository $customerDetailsRepository,
        TierAssignTypeProvider $tierAssignTypeProvider,
        ExcludeDeliveryCostsProvider $excludeDeliveryCostProvider,
        SettingsManager $settingsManager
    ) {
        $this->accountDetailsRepository = $accountDetailsRepository;
        $this->levelRepository = $levelRepository;
        $this->customerDetailsRepository = $customerDetailsRepository;
        $this->tierAssignTypeProvider = $tierAssignTypeProvider;
        $this->excludeDeliveryCostProvider = $excludeDeliveryCostProvider;
        $this->settingsManager = $settingsManager;
    }

    /**
     * @param CustomerId $customerId
     *
     * @return CustomerStatus
     */
    public function getStatus(CustomerId $customerId)
    {
        $status = new CustomerStatus($customerId);
        $status->setCurrency($this->getCurrency());

        $customer = $this->getCustomerDetails($customerId);
        if (!$customer) {
            return $status;
        }

        $status->setFirstName($customer->getFirstName());
        $status->setLastName($customer->getLastName());

        $accountDetails = $this->getAccountDetails($customerId);
        /** @var Level $level */
        $level = $customer->getLevelId() ?
            $this->levelRepository->byId(new \OpenLoyalty\Domain\Level\LevelId($customer->getLevelId()->__toString()))
            : null;

        $nextLevel = null;
        $conditionValue = 0;

        $tierAssignType = $this->tierAssignTypeProvider->getType();
        if ($tierAssignType == TierAssignTypeProvider::TYPE_POINTS) {
            if ($accountDetails) {
                $conditionValue = $accountDetails->getAvailableAmount();
            }
        } elseif ($tierAssignType == TierAssignTypeProvider::TYPE_TRANSACTIONS) {
            if ($this->excludeDeliveryCostProvider->areExcluded()) {
                $conditionValue = $customer->getTransactionsAmountWithoutDeliveryCosts() - $customer->getAmountExcludedForLevel();
            } else {
                $conditionValue = $customer->getTransactionsAmount() - $customer->getAmountExcludedForLevel();
            }
        }

        /** @var Level $nextLevel */
        $nextLevel = $level ?
            $this->levelRepository->findNextLevelByConditionValueWithTheBiggestReward($conditionValue, $level->getConditionValue())
            : null;

        if ($accountDetails) {
            $status->setPoints($accountDetails->getAvailableAmount());
            $status->setUsedPoints($accountDetails->getUsedAmount());
            $status->setExpiredPoints($accountDetails->getExpiredAmount());

            $status->setTransactionsAmount($customer->getTransactionsAmount());
            $status->setTransactionsAmountWithoutDeliveryCosts($customer->getTransactionsAmountWithoutDeliveryCosts());
            $status->setAverageTransactionsAmount(number_format($customer->getAverageTransactionAmount(), 2, '.', ''));
            $status->setTransactionsCount($customer->getTransactionsCount());
        }

        if ($level) {
            $status->setLevelName($level->getName());
            $status->setLevelPercent(number_format($level->getReward()->getValue() * 100, 2).'%');
        }

        if ($nextLevel) {
            $status->setNextLevelName($nextLevel->getName());
            $status->setNextLevelPercent(number_format($nextLevel->getReward()->getValue() * 100, 2).'%');
        }

        if ($nextLevel && $accountDetails) {
            $this->applyNextLevelRequirements($customer, $status, $nextLevel, $accountDetails->getAvailableAmount());
        }

        return $status;
    }

    protected function applyNextLevelRequirements(CustomerDetails $customer, CustomerStatus $status, Level $nextLevel, $currentPoints)
    {
        $tierAssignType = $this->tierAssignTypeProvider->getType();

        if ($tierAssignType == TierAssignTypeProvider::TYPE_POINTS) {
            $status->setPointsToNextLevel($nextLevel->getConditionValue() - $currentPoints);
        } elseif ($tierAssignType == TierAssignTypeProvider::TYPE_TRANSACTIONS) {
            if ($this->excludeDeliveryCostProvider->areExcluded()) {
                $currentAmount = $customer->getTransactionsAmountWithoutDeliveryCosts() - $customer->getAmountExcludedForLevel();
                $status->setTransactionsAmountToNextLevelWithoutDeliveryCosts(($nextLevel->getConditionValue() - $currentAmount));
            } else {
                $currentAmount = $customer->getTransactionsAmount() - $customer->getAmountExcludedForLevel();
                $status->setTransactionsAmountToNextLevel(($nextLevel->getConditionValue() - $currentAmount));
            }
        }
    }

    /**
     * @param CustomerId $customerId
     *
     * @return null|AccountDetails
     */
    protected function getAccountDetails(CustomerId $customerId)
    {
        $accounts = $this->accountDetailsRepository->findBy(['customerId' => $customerId->__toString()]);
        if (count($accounts) == 0) {
            return;
        }
        /** @var AccountDetails $account */
        $account = reset($accounts);

        if (!$account instanceof AccountDetails) {
            return;
        }

        return $account;
    }

    /**
     * @param CustomerId $customerId
     *
     * @return CustomerDetails
     */
    protected function getCustomerDetails(CustomerId $customerId)
    {
        return $this->customerDetailsRepository->find($customerId->__toString());
    }

    protected function getCurrency()
    {
        $currency = $this->settingsManager->getSettingByKey('currency');
        if ($currency) {
            return $currency->getValue();
        }

        return 'PLN';
    }
}
