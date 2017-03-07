<?php

namespace OpenLoyalty\Infrastructure\Segment\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\Segment\SegmentId;
use Rhumsaa\Uuid\Doctrine\UuidType;

/**
 * Class SegmentIdDoctrineType.
 */
class SegmentIdDoctrineType extends UuidType
{
    const NAME = 'segment_id';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return;
        }

        if ($value instanceof SegmentId) {
            return $value;
        }

        return new SegmentId($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null == $value) {
            return;
        }

        if ($value instanceof SegmentId) {
            return $value->__toString();
        }

        return;
    }

    public function getName()
    {
        return self::NAME;
    }
}
