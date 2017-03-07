<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Infrastructure\Model\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;
use OpenLoyalty\Domain\Model\SKU;

/**
 * Class SKUDoctrineType.
 */
class SKUDoctrineType extends StringType
{
    const NAME = 'sku';

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return;
        }

        if ($value instanceof SKU) {
            return $value;
        }

        return new SKU($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null == $value) {
            return;
        }

        if ($value instanceof SKU) {
            return $value->getCode();
        }

        return;
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
        return self::NAME;
    }
}
