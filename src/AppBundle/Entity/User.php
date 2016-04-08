<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Validator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email is already registered")
 * @UniqueEntity(
 *     fields={"username"},
 *     message="This username is not available")
 *
 */
class User implements UserInterface, AdvancedUserInterface, EquatableInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var String
     * @Validator\NotBlank()
     * @Validator\Regex(
     *     pattern="^@\w{1,15}^",
     *     match=true,
     *     message="The twitter account must begin with @"
     * )
     * @ORM\Column(name="twitter", type="string", length=255)
     */
    protected $twitter_user;

    /**
     * @ORM\Column(name="username", type="string", length=25, unique=true)
     *
     * @Validator\NotBlank()
     * @Validator\Length(
     *      min = 6,
     *      max = 25,
     *      minMessage = "Your username must be at least {{ limit }} characters long",
     *      maxMessage = "Your username cannot be longer than {{ limit }} characters"
     * )
     */
    private $username;

    /**
     * @ORM\Column(name="salt", type="string", length=80)
     */
    private $salt;

    /**
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var String
     * @Validator\NotBlank()
     * @Validator\Length(
     *      min = 6,
     *      max = 10,
     *      minMessage = "Your password must be at least {{ limit }} characters long",
     *      maxMessage = "Your password cannot be longer than {{ limit }} characters"
     * )
     */
    private $plain_password;

    /**
     * @ORM\Column(name="email", type="string", length=60, unique=true)
     * @Validator\NotBlank()
     * @Validator\Email()
     */
    private $email;

    /**
     * @ORM\Column(name="profile_picture", type="string")
     */
    private $profile_picture;

    /**
     * @var ArrayCollection
     * @ORM\Column(name="roles", type="array")
     */
    private $roles;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="is_enabled", type="boolean")
     */
    private $isEnabled;

    /**
     * @var bool
     * @ORM\Column(name="is_expired", type="boolean")
     */
    private $expired;

    /**
     * @var bool
     * @ORM\Column(name="is_credentialsExpired", type="boolean")
     */
    private $credentialsExpired;

    /**
     * @var bool
     * @ORM\Column(name="is_locked", type="boolean")
     */
    private $locked;

    public function __construct()
    {
        $this->isActive = false;
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $this->roles = new ArrayCollection();
        $this->roles->add("ROLE_USER");

        $this->locked = false;
        $this->expired = false;
        $this->isEnabled = false;
        $this->credentialsExpired = false;
    }


    /**
     * @return String
     */
    public function getTwitterUser()
    {
        return $this->twitter_user;
    }

    /**
     * @param String $twitter_user
     */
    public function setTwitterUser($twitter_user)
    {
        $this->twitter_user = $twitter_user;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function eraseCredentials()
    {
        $this->plain_password = "";
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function isAccountNonExpired()
    {
        return $this->expired;
    }

    public function isAccountNonLocked()
    {
        return $this->locked;
    }

    public function isCredentialsNonExpired()
    {
        return $this->credentialsExpired;
    }

    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plain_password;
    }

    /**
     * @param mixed $plain_password
     */
    public function setPlainPassword($plain_password)
    {
        $this->plain_password = $plain_password;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @return boolean
     */
    public function isLocked()
    {
        return $this->locked;
    }


    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set roles
     *
     * @param array $roles
     *
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled
     *
     * @return User
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set expired
     *
     * @param boolean $expired
     *
     * @return User
     */
    public function setExpired($expired)
    {
        $this->expired = $expired;

        return $this;
    }

    /**
     * Get expired
     *
     * @return boolean
     */
    public function getExpired()
    {
        return $this->expired;
    }

    /**
     * Set credentialsExpired
     *
     * @param boolean $credentialsExpired
     *
     * @return User
     */
    public function setCredentialsExpired($credentialsExpired)
    {
        $this->credentialsExpired = $credentialsExpired;

        return $this;
    }

    /**
     * Get credentialsExpired
     *
     * @return boolean
     */
    public function getCredentialsExpired()
    {
        return $this->credentialsExpired;
    }

    /**
     * Set locked
     *
     * @param boolean $locked
     *
     * @return User
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * Get locked
     *
     * @return boolean
     */
    public function getLocked()
    {
        return $this->locked;
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($this->password !== $user->getPassword()) {
            return false;
        }

        if ($this->salt !== $user->getSalt()) {
            return false;
        }

        if ($this->username !== $user->getUsername()) {
            return false;
        }

        if ($this->email !== $user->getEmail()) {
            return false;
        }

        if ($this->twitter_user !== $user->getTwitterUser()) {
            return false;
        }

        return true;
    }

    public function generateConfirmationToken(){

        return time() . hash("sha512", $this->email);

    }



    /**
     * Set profilePicture
     *
     * @param string $profilePicture
     *
     * @return User
     */
    public function setProfilePicture($profilePicture)
    {
        $this->profile_picture = $profilePicture;

        return $this;
    }

    /**
     * Get profilePicture
     *
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->profile_picture;
    }
}
