<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\SettingsBundle\Validator\Constraints;

use OpenLoyalty\Bundle\SettingsBundle\Model\TranslationsEntry;
use OpenLoyalty\Bundle\SettingsBundle\Service\TranslationsProvider;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class UniqueKeyValidator.
 */
class UniqueKeyValidator extends ConstraintValidator
{
    /**
     * @var TranslationsProvider
     */
    protected $translationsProvider;

    /**
     * UniqueKeyValidator constructor.
     *
     * @param TranslationsProvider $translationsProvider
     */
    public function __construct(TranslationsProvider $translationsProvider)
    {
        $this->translationsProvider = $translationsProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueKey) {
            return;
        }

        if (!$value instanceof TranslationsEntry) {
            return;
        }

        if ($value->getKey() == $value->getPreviousKey()) {
            return;
        }

        if ($this->translationsProvider->hasTranslation($value->getKey())) {
            $this->context->buildViolation($constraint->message)->atPath($constraint->errorPath)->addViolation();
        }
    }
}
