<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Infrastructure\Audit\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use OpenLoyalty\Domain\Audit\AuditLogId;
use Rhumsaa\Uuid\Doctrine\UuidType;

/**
 * Class AuditLogIdDoctrineType.
 */
final class AuditLogIdDoctrineType extends UuidType
{
    /**
     *
     */
    const NAME = 'audit_log_id';

    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (empty($value)) {
            return;
        }

        if ($value instanceof AuditLogId) {
            return $value;
        }

        return new AuditLogId($value);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof AuditLogId) {
            return $value->__toString();
        }

        return;
    }
}
