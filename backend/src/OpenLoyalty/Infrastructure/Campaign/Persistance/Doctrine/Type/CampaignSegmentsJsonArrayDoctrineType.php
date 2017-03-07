<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Infrastructure\Campaign\Persistance\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use OpenLoyalty\Domain\Campaign\SegmentId;

/**
 * Class CampaignSegmentsJsonArrayDoctrineType.
 */
class CampaignSegmentsJsonArrayDoctrineType extends Type
{
    const NAME = 'campaign_segments_json_array';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getJsonTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!is_array($value)) {
            return json_encode([]);
        }

        $serialized = [];
        /** @var SegmentId $segmentId */
        foreach ($value as $segmentId) {
            $serialized[] = $segmentId->__toString();
        }

        return json_encode($serialized);
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return [];
        }

        $value = (is_resource($value)) ? stream_get_contents($value) : $value;

        $decoded = json_decode($value, true);

        if (!$decoded) {
            return [];
        }

        $labels = [];

        foreach ($decoded as $item) {
            $labels[] = new SegmentId($item);
        }

        return $labels;
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
