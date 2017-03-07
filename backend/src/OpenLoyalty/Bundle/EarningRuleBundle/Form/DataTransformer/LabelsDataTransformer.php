<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\EarningRuleBundle\Form\DataTransformer;

use OpenLoyalty\Domain\Model\Label;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class LabelsDataTransformer.
 */
class LabelsDataTransformer implements DataTransformerInterface
{
    protected $delimiter = ';';

    /**
     * @param mixed $value The value in the original representation
     *
     * @return mixed The value in the transformed representation
     *
     * @throws TransformationFailedException When the transformation fails.
     */
    public function transform($value)
    {
        if ($value == null) {
            return;
        }
        if (!is_array($value)) {
            throw new \InvalidArgumentException();
        }
        $values = array_map(function (Label $label) {
            return $label->getKey().':'.$label->getValue();
        }, $value);

        return implode($this->delimiter, $values);
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
        $values = explode($this->delimiter, $value);
        $transformed = array_map(function ($code) {
            if (!$code) {
                return;
            }

            $value = explode(':', $code);

            return new Label($value[0], $value[1]);
        }, $values);

        $transformed = array_filter($transformed, function ($element) {
            if ($element == null) {
                return false;
            }

            return true;
        });

        return $transformed;
    }
}
