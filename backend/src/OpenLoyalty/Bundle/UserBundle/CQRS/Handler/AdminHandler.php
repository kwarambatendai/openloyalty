<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */

namespace OpenLoyalty\Bundle\UserBundle\CQRS\Handler;

use Broadway\CommandHandling\CommandHandlerInterface;
use Broadway\UuidGenerator\UuidGeneratorInterface;
use OpenLoyalty\Bundle\UserBundle\CQRS\Command\CreateAdmin;
use OpenLoyalty\Bundle\UserBundle\CQRS\Command\EditAdmin;
use OpenLoyalty\Bundle\UserBundle\CQRS\Command\SelfEditAdmin;
use OpenLoyalty\Bundle\UserBundle\Entity\Admin;
use OpenLoyalty\Bundle\UserBundle\Entity\Repository\AdminRepository;
use OpenLoyalty\Bundle\UserBundle\Exception\EmailAlreadyExistException;
use OpenLoyalty\Bundle\UserBundle\Service\UserManager;

/**
 * Class AdminHandler.
 */
class AdminHandler implements CommandHandlerInterface
{
    /**
     * @var UserManager
     */
    protected $userManager;

    /**
     * @var UuidGeneratorInterface
     */
    protected $uuidGenerator;

    /**
     * @var AdminRepository
     */
    protected $adminRepository;

    /**
     * AdminHandler constructor.
     *
     * @param UserManager            $userManager
     * @param UuidGeneratorInterface $uuidGenerator
     * @param AdminRepository        $adminRepository
     */
    public function __construct(
        UserManager $userManager,
        UuidGeneratorInterface $uuidGenerator,
        AdminRepository $adminRepository
    ) {
        $this->userManager = $userManager;
        $this->uuidGenerator = $uuidGenerator;
        $this->adminRepository = $adminRepository;
    }

    /**
     * @param mixed $command
     */
    public function handle($command)
    {
        switch (true) {
            case $command instanceof CreateAdmin:
                return $this->handleCreateAdmin($command);
            case $command instanceof EditAdmin:
                return $this->handleEditAdmin($command);
            case $command instanceof SelfEditAdmin:
                return $this->handleSelfEditAdmin($command);
        }
    }

    protected function handleCreateAdmin(CreateAdmin $command)
    {
        if ($this->adminRepository->isEmailExist($command->email)) {
            throw new EmailAlreadyExistException();
        }
        $id = $this->uuidGenerator->generate();
        $admin = $this->userManager->createNewAdmin($id);
        $admin->setApiKey($command->apiKey);
        $admin->setEmail($command->email);
        $admin->setFirstName($command->firstName);
        $admin->setLastName($command->lastName);
        $admin->setPhone($command->phone);
        $admin->setPlainPassword($command->plainPassword);
        $admin->setExternal($command->external);
        $admin->setIsActive($command->isActive);
        $this->userManager->updateUser($admin);
    }

    protected function handleEditAdmin(EditAdmin $command)
    {
        if ($this->adminRepository->isEmailExist($command->email, $command->admin->getId())) {
            throw new EmailAlreadyExistException();
        }
        $admin = $command->admin;
        $admin->setApiKey($command->apiKey);
        $admin->setEmail($command->email);
        $admin->setFirstName($command->firstName);
        $admin->setLastName($command->lastName);
        $admin->setPhone($command->phone);
        $admin->setPlainPassword($command->plainPassword);
        $admin->setExternal($command->external);
        $admin->setIsActive($command->isActive);
        $this->userManager->updateUser($admin);
    }

    protected function handleSelfEditAdmin(SelfEditAdmin $command)
    {
        if ($this->adminRepository->isEmailExist($command->email, $command->admin->getId())) {
            throw new EmailAlreadyExistException();
        }
        $admin = $command->admin;
        $admin->setEmail($command->email);
        $admin->setFirstName($command->firstName);
        $admin->setLastName($command->lastName);
        $admin->setPhone($command->phone);
        $this->userManager->updateUser($admin);
    }
}
