<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\EarningRule;

use OpenLoyalty\Domain\EarningRule\Model\UsageSubject;

interface EarningRuleUsageRepository
{
    public function byId(EarningRuleUsageId $earningRuleUsageId);

    public function findAll();

    public function save(EarningRule $earningRule);

    public function remove(EarningRule $earningRule);

    public function countDailyUsage(EarningRuleId $earningRuleId, UsageSubject $subject);

    public function countWeeklyUsage(EarningRuleId $earningRuleId, UsageSubject $subject);

    public function countMonthlyUsage(EarningRuleId $earningRuleId, UsageSubject $subject);
}
