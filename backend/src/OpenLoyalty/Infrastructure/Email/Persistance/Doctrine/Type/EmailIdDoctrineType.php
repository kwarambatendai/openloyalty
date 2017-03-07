<?php
/*
 * This file is part of the "OpenLoyalty" package.
 *
 * (c) Divante Sp. z o. o.
 *
 * Author: Cezary Olejarczyk
 * Date: 02.02.17 14:10
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
