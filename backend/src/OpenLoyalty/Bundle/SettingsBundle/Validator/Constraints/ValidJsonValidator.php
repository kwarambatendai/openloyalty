<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\SettingsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class ValidJsonValidator.
 */
class ValidJsonValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ValidJson || !$value) {
            return;
        }

        $test = json_decode($value, true);
        if (null === $test) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
