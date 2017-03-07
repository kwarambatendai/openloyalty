<?php

namespace OpenLoyalty\Bundle\UserBundle\CQRS\Command;

use OpenLoyalty\Bundle\UserBundle\Entity\Admin;
use OpenLoyalty\Bundle\UserBundle\Validator\Constraint\PasswordRequirements;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class EditAdmin.
 */
class EditAdmin extends AdminCommand
{
    /**
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

    public function __construct(Admin $admin)
    {
        $this->admin = $admin;
    }
}
