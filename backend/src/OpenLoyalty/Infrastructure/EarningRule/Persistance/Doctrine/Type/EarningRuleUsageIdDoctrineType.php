<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\EarningRule\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\EarningRule\EarningRuleUsageId;
use Rhumsaa\Uuid\Doctrine\UuidType;

/**
 * Class EarningRuleUsageIdDoctrineType.
 */
class EarningRuleUsageIdDoctrineType extends UuidType
{
    const NAME = 'earning_rule_usage_id';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return;
        }

        if ($value instanceof EarningRuleUsageId) {
            return $value;
        }

        return new EarningRuleUsageId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null == $value) {
            return;
        }

        if ($value instanceof EarningRuleUsageId) {
            return $value->__toString();
        }

        return;
    }

    public function getName()
    {
        return self::NAME;
    }
}
