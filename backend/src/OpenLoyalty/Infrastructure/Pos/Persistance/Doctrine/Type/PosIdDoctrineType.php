<?php

namespace OpenLoyalty\Infrastructure\Pos\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\Pos\PosId;
use Rhumsaa\Uuid\Doctrine\UuidType;

/**
 * Class PosIdDoctrineType.
 */
class PosIdDoctrineType extends UuidType
{
    const NAME = 'pos_id';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return;
        }

        if ($value instanceof PosId) {
            return $value;
        }

        return new PosId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null == $value) {
            return;
        }

        if ($value instanceof PosId) {
            return $value->__toString();
        }

        return;
    }

    public function getName()
    {
        return self::NAME;
    }
}
