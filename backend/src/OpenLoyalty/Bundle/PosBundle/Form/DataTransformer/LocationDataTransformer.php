<?php

namespace OpenLoyalty\Bundle\PosBundle\Form\DataTransformer;

use Assert\AssertionFailedException;
use OpenLoyalty\Domain\Pos\Model\Location;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class LocationDataTransformer.
 */
class LocationDataTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        if (null == $value) {
            return;
        }

        if (!$value instanceof Location) {
            throw new InvalidArgumentException();
        }

        return $value->serialize();
    }

    /**
     * @param mixed $value The value in the transformed representation
     *
     * @return mixed The value in the original representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function reverseTransform($value)
    {
        if ($value == null) {
            return;
        }

        try {
            return Location::deserialize($value);
        } catch (AssertionFailedException $e) {
            return new Location(
                isset($data['street']) ? $data['street'] : null,
                isset($data['address1']) ? $data['address1'] : null,
                isset($data['province']) ? $data['province'] : null,
                isset($data['city']) ? $data['city'] : null,
                isset($data['postal']) ? $data['postal'] : null,
                isset($data['country']) ? $data['country'] : null,
                isset($data['address2']) ? $data['address2'] : null,
                isset($data['lat']) ? $data['lat'] : null,
                isset($data['long']) ? $data['long'] : null,
                true
            );
        }
    }
}
