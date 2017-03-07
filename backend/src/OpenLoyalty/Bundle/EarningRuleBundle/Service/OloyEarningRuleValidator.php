<?php

namespace OpenLoyalty\Bundle\EarningRuleBundle\Service;

use OpenLoyalty\Domain\Account\CustomerId;
use OpenLoyalty\Domain\EarningRule\CustomEventEarningRule;
use OpenLoyalty\Domain\EarningRule\EarningRuleId;
use OpenLoyalty\Domain\EarningRule\EarningRuleLimit;
use OpenLoyalty\Domain\EarningRule\EarningRuleRepository;
use OpenLoyalty\Domain\EarningRule\EarningRuleUsageRepository;
use OpenLoyalty\Domain\EarningRule\Model\UsageSubject;
use OpenLoyalty\Infrastructure\Account\EarningRuleLimitValidator;
use OpenLoyalty\Infrastructure\Account\Exception\EarningRuleLimitExceededException;

/**
 * Class OloyEarningRuleValidator.
 */
class OloyEarningRuleValidator implements EarningRuleLimitValidator
{
    /**
     * @var EarningRuleUsageRepository
     */
    protected $earningRuleUsageRepository;

    /**
     * @var EarningRuleRepository
     */
    protected $earningRuleRepository;

    /**
     * OloyEarningRuleValidator constructor.
     *
     * @param EarningRuleUsageRepository $earningRuleUsageRepository
     * @param EarningRuleRepository      $earningRuleRepository
     */
    public function __construct(
        EarningRuleUsageRepository $earningRuleUsageRepository,
        EarningRuleRepository $earningRuleRepository
    ) {
        $this->earningRuleUsageRepository = $earningRuleUsageRepository;
        $this->earningRuleRepository = $earningRuleRepository;
    }

    /**
     * @param $earningRuleId
     * @param CustomerId $customerId
     *
     * @throws EarningRuleLimitExceededException
     */
    public function validate($earningRuleId, CustomerId $customerId)
    {
        $repo = $this->earningRuleUsageRepository;
        $earningRuleId = new EarningRuleId($earningRuleId);
        /** @var CustomEventEarningRule $earningRule */
        $earningRule = $this->earningRuleRepository->byId($earningRuleId);
        if (!$earningRule instanceof CustomEventEarningRule) {
            return;
        }
        $limit = $earningRule->getLimit();
        if (!$limit || !$limit->isActive()) {
            return;
        }
        $subject = new UsageSubject($customerId->__toString());

        switch ($limit->getPeriod()) {
            case EarningRuleLimit::PERIOD_DAY:
                $usage = $repo->countDailyUsage($earningRuleId, $subject);
                break;
            case EarningRuleLimit::PERIOD_WEEK:
                $usage = $repo->countWeeklyUsage($earningRuleId, $subject);
                break;
            case EarningRuleLimit::PERIOD_MONTH:
                $usage = $repo->countMonthlyUsage($earningRuleId, $subject);
                break;
            default:
                $usage = 0;
        }
        if ($usage >= $limit->getLimit()) {
            throw new EarningRuleLimitExceededException();
        }
    }
}
