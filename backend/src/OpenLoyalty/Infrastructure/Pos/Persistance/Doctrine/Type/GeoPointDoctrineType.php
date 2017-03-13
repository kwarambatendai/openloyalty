<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Pos\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use OpenLoyalty\Domain\Pos\Model\GeoPoint;

/**
 * Class GeoPointDoctrineType.
 */
class GeoPointDoctrineType extends Type
{
    const NAME = 'ol_geo_point';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getJsonTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value instanceof GeoPoint) {
            return;
        }

        return json_encode($value->serialize());
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return;
        }

        $value = (is_resource($value)) ? stream_get_contents($value) : $value;

        $decoded = json_decode($value, true);

        if (!$decoded) {
            return;
        }

        return GeoPoint::deserialize($decoded);
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

    public function getName()
    {
        return static::NAME;
    }
}
