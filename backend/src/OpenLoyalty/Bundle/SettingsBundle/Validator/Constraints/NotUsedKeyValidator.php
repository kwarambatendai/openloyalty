<?php

namespace OpenLoyalty\Bundle\SettingsBundle\Validator\Constraints;

use OpenLoyalty\Bundle\SettingsBundle\Model\TranslationsEntry;
use OpenLoyalty\Bundle\SettingsBundle\Service\TranslationsProvider;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class NotUsedKeyValidator.
 *
 * @Annotation
 */
class NotUsedKeyValidator extends ConstraintValidator
{
    /**
     * @var TranslationsProvider
     */
    protected $translationsProvider;

    /**
     * NotUsedKeyValidator constructor.
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
        if (!$constraint instanceof NotUsedKey) {
            return;
        }

        if (!$value instanceof TranslationsEntry) {
            return;
        }

        if (!$value->getName() || $value->getKey() == $value->getPreviousKey()) {
            return;
        }

        $currentTranslations = $this->translationsProvider->getCurrentTranslations();

        if ($currentTranslations->getKey() == $value->getPreviousKey()) {
            $this->context->buildViolation($constraint->message)->atPath($constraint->errorPath)->addViolation();
        }
    }
}
