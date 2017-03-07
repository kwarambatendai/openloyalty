<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\CampaignBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenLoyalty\Domain\Campaign\LevelId;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class LevelsDataTransformer.
 */
class LevelsDataTransformer implements DataTransformerInterface
{
    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if ($value == null) {
            return $value;
        }

        $tmp = [];

        if ($value instanceof ArrayCollection || is_array($value)) {
            foreach ($value as $v) {
                if ($v instanceof LevelId) {
                    $tmp[] = $v->__toString();
                }
            }
        }

        return $tmp;
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if ($value == null) {
            return $value;
        }

        $tmp = [];

        if ($value instanceof ArrayCollection || is_array($value)) {
            foreach ($value as $v) {
                $tmp[] = new LevelId($v);
            }
        }

        return $tmp;
    }
}
