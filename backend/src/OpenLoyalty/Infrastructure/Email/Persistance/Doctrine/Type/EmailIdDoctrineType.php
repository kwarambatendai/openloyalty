<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Email\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\Email\EmailId;
use Rhumsaa\Uuid\Doctrine\UuidType;

/**
 * Class EmailIdDoctrineType.
 */
class EmailIdDoctrineType extends UuidType
{
    const NAME = 'email_id';

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return;
        }

        if ($value instanceof EmailId) {
            return $value;
        }

        return new EmailId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof EmailId) {
            return $value->__toString();
        }

        if (!empty($value)) {
            return $value;
        }

        return;
    }
}
