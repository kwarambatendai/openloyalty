<?php

namespace OpenLoyalty\Bundle\SettingsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class ValidJson.
 *
 * @Annotation
 */
class ValidJson extends Constraint
{
    public $message = 'This is not a valid json';

    public function getTargets()
    {
        return static::PROPERTY_CONSTRAINT;
    }
}
