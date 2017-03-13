<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\SettingsBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Class UniqueKey.
 *
 * @Annotation
 */
class UniqueKey extends Constraint
{
    public $message = 'This name already exists';

    public $errorPath = 'name';

    public function getTargets()
    {
        return static::CLASS_CONSTRAINT;
    }
}
