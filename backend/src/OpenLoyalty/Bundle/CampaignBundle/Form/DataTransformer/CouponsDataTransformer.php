<?php

namespace OpenLoyalty\Bundle\CampaignBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use OpenLoyalty\Domain\Campaign\Model\Coupon;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class CouponsDataTransformer.
 */
class CouponsDataTransformer implements DataTransformerInterface
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
                if ($v instanceof Coupon) {
                    $tmp[] = $v->getCode();
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
                $tmp[] = new Coupon($v);
            }
        }

        return $tmp;
    }
}
