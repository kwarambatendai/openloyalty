<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\Validator\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Class PasswordRequirementsValidator.
 */
class PasswordRequirementsValidator extends ConstraintValidator
{
    /**
     * @param string                          $value
     * @param PasswordRequirements|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        if (null === $value || '' === $value) {
            return;
        }
        if ($constraint->minLength > 0 && (strlen($value) < $constraint->minLength)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->tooShortMessage)
                    ->setParameters(array('{{length}}' => $constraint->minLength))
                    ->setInvalidValue($value)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->tooShortMessage, array('{{length}}' => $constraint->minLength), $value);
            }
        }
        if ($constraint->requireLetters && !preg_match('/\pL/', $value)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->missingLettersMessage)
                    ->setInvalidValue($value)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->missingLettersMessage, array(), $value);
            }
        }
        if ($constraint->requireCaseDiff && !preg_match('/(\p{Ll}+.*\p{Lu})|(\p{Lu}+.*\p{Ll})/', $value)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->requireCaseDiffMessage)
                    ->setInvalidValue($value)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->requireCaseDiffMessage, array(), $value);
            }
        }
        if ($constraint->requireNumbers && !preg_match('/\pN/', $value)) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->missingNumbersMessage)
                    ->setInvalidValue($value)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->missingNumbersMessage, array(), $value);
            }
        }
        if ($constraint->requireSpecialCharacter &&
            !preg_match('/[^p{Ll}\p{Lu}\pL\pN]/', $value)
        ) {
            if ($this->context instanceof ExecutionContextInterface) {
                $this->context->buildViolation($constraint->missingSpecialCharacterMessage)
                    ->setInvalidValue($value)
                    ->addViolation();
            } else {
                $this->context->addViolation($constraint->missingSpecialCharacterMessage, array(), $value);
            }
        }
    }
}
