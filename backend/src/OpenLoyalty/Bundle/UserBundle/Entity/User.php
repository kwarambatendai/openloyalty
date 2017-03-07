<?php

namespace OpenLoyalty\Bundle\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="ol__user")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @JMS\ExclusionPolicy("all")
 * @UniqueEntity(
 *  fields={"username"},
 *  message="This username is already exist"
 * )
 */
abstract class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="string")
     * @ORM\Id
     * @JMS\Expose()
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=128, unique=true)
     * @JMS\Expose()
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=128)
     */
    protected $password;

    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     */
    protected $salt;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     * @JMS\Expose()
     */
    protected $isActive;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose()
     */
    protected $createAt;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(name="ol__users_roles")
     */
    protected $roles;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Email(groups={"registration"})
     * @JMS\Expose()
     */
    protected $email;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @var string
     * @ORM\Column(type="string", name="confirmation_token", nullable=true)
     */
    protected $confirmationToken;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true, name="deleted_at")
     */
    protected $deletedAt = null;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true, name="last_login_at")
     */
    protected $lastLoginAt = null;

    public function __construct($id)
    {
        $this->id = $id;
        $this->username = $id;
        $this->createAt = new \DateTime('now');
        $this->isActive = true;
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->roles = new ArrayCollection();
    }

    /**
     * @see \Serializable::serialize()
     */
    public function serialize()
    {
        return \serialize(array(
            $this->id,
            $this->username,
        ));
    }

    /**
     * @see \Serializable::unserialize()
     */
    public function unserialize($serialized)
    {
        list($this->id, $this->username) = \unserialize($serialized);
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return mixed
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * @param mixed $createAt
     */
    public function setCreateAt($createAt)
    {
        $this->createAt = $createAt;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {
        if (!is_array($this->roles)) {
            $roles = $this->roles->toArray();
        } else {
            $roles = $this->roles;
        }

        return $roles;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordRequestedAt()
    {
        return $this->passwordRequestedAt;
    }

    /**
     * @param \DateTime $passwordRequestedAt
     */
    public function setPasswordRequestedAt($passwordRequestedAt)
    {
        $this->passwordRequestedAt = $passwordRequestedAt;
    }

    public function eraseCredentials()
    {
    }

    public function hasRole($role)
    {
        if ($this->findUserRole($role)) {
            return true;
        }

        return false;
    }

    public function addRole($role)
    {
        if (!$role instanceof Role) {
            throw new \Exception('addRole takes a Role object as the parameter');
        }

        if (!$this->hasRole($role->getRole())) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole($role)
    {
        $roleElement = $this->findUserRole($role);
        if ($roleElement) {
            $this->roles->removeElement($roleElement);
        }
    }

    public function findUserRole($role)
    {
        /** @var Role $roleItem */
        foreach ($this->getRoles() as $roleItem) {
            if ($role == $roleItem->getRole()) {
                return $roleItem;
            }
        }

        return;
    }

    /**
     * @return \DateTime|null
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * @param \DateTime|null $deletedAt
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    public function isPasswordRequestNonExpired($ttl)
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime &&
        $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * @return string
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;
    }

    /**
     * @return \DateTime
     */
    public function getLastLoginAt()
    {
        return $this->lastLoginAt;
    }

    /**
     * @param \DateTime $lastLoginAt
     */
    public function setLastLoginAt($lastLoginAt)
    {
        $this->lastLoginAt = $lastLoginAt;
    }
}
