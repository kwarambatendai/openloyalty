<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\CQRS\Command;

use Symfony\Component\Validator\Constraints as Assert;
use OpenLoyalty\Bundle\UserBundle\Validator\Constraint\PasswordRequirements;

/**
 * Class CreateAdmin.
 */
class CreateAdmin extends AdminCommand
{
    /**
     * @Assert\NotBlank(groups={"internal"})
     * @PasswordRequirements(
     *     requireSpecialCharacter=true,
     *     requireNumbers=true,
     *     requireLetters=true,
     *     requireCaseDiff=true,
     *     minLength="8"
     * )
     */
    public $plainPassword;

    public $external;

    /**
     * @Assert\NotBlank(groups={"external"})
     */
    public $apiKey;

    public $isActive;
}
