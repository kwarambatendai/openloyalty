<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\SettingsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class NotUsedKey.
 *
 * @Annotation
 */
class NotUsedKey extends Constraint
{
    public $message = 'This translation is set as current translations. Name cannot be changed.';

    public $errorPath = 'name';

    public function getTargets()
    {
        return static::CLASS_CONSTRAINT;
    }
}
