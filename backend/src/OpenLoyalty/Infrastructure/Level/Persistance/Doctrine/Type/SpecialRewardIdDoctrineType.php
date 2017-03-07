<?php

namespace OpenLoyalty\Infrastructure\Level\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\Level\SpecialRewardId;
use Rhumsaa\Uuid\Doctrine\UuidType;

/**
 * Class SpecialRewardIdDoctrineType.
 */
final class SpecialRewardIdDoctrineType extends UuidType
{
    const NAME = 'special_reward_id';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return;
        }

        if ($value instanceof SpecialRewardId) {
            return $value;
        }

        return new SpecialRewardId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null == $value) {
            return;
        }

        if ($value instanceof SpecialRewardId) {
            return $value->__toString();
        }

        return;
    }

    public function getName()
    {
        return self::NAME;
    }
}
