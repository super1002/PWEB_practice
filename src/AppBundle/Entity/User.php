<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Encoder\EncoderAwareInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Validator;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

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
 *
 */
class User implements UserInterface, AdvancedUserInterface, EquatableInterface, EncoderAwareInterface
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
     *
     * @ORM\Column(name="profile_picture", type="string", nullable=true)
     */
    private $profile_picture;

    /**
     * @Validator\File()
     * @var UploadedFile
     */
    private $file;

    /**
     * @var array
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

    /**
     * @var float
     * @ORM\Column(name="balance", type="float")
     * @Validator\NotBlank()
     * @Validator\Range(
     *     min="0",
     *     minMessage="You cannot have negative balance, this is not a charity",
     *     max="1000",
     *     maxMessage="For security reason you cannot recharge when your balance is over 1000€"
     * )
     */
    private $balance;


    /**
     * @var float
     * @Validator\NotBlank(groups={"recharge"})
     * @Validator\Range(
     *     min="1",
     *     minMessage="You must recharge at least 1€",
     *     max="100",
     *     maxMessage="You cannot do a recharge higher than 100€",
     *     groups={"recharge"}
     * )
     */
    private $recharge;


    /**
     * @var ArrayCollection $products
     * @ORM\OneToMany(targetEntity="Product", mappedBy="owner")
     */
    private $products;

    /**
     * @var integer
     * @ORM\Column(name="score", type="integer")
     */
    private $score;

    /**
     * @var ArrayCollection $comments
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="target")
     */
    private $comments;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="author")
     */
    private $comments_done;


    public function __construct()
    {
        $this->isActive = false;
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);

        $this->roles = array("ROLE_USER");
        $this->path = null;
        $this->file = null;
        $this->balance = 0;
        $this->recharge = 0;
        $this->products = new ArrayCollection();
        $this->comments = new ArrayCollection();

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
        //return array("ROLE_USER");
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
        return !($this->expired);
    }

    public function isAccountNonLocked()
    {
        return !($this->locked);
    }

    public function isCredentialsNonExpired()
    {
        return !($this->credentialsExpired);
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
     * Sets file.
     *
     * @param string $profilePicture
     */
    public function setProfilePicture($profilePicture = null)
    {
        $this->profile_picture = $profilePicture;
    }

    /**
     * Get file.
     *
     * @return string
     */
    public function getProfilePicture()
    {
        return $this->profile_picture;
    }

    /**
     * @return UploadedFile mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile(UploadedFile $file)
    {
        $this->file = $file;
    }


    public function getEncoderName()
    {
        return 'bcrypt_encoder';
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;
    }

    /**
     * @return float
     */
    public function getRecharge()
    {
        return $this->recharge;
    }

    /**
     * @param float $recharge
     */
    public function setRecharge($recharge)
    {
        $this->recharge = $recharge;
    }




    /**
     * Add product
     *
     * @param \AppBundle\Entity\Product $product
     *
     * @return User
     */
    public function addProduct(\AppBundle\Entity\Product $product)
    {
        $this->products[] = $product;

        return $this;
    }

    /**
     * Remove product
     *
     * @param \AppBundle\Entity\Product $product
     */
    public function removeProduct(\AppBundle\Entity\Product $product)
    {
        $this->products->removeElement($product);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param int $score
     */
    public function setScore($score)
    {
        $this->score = $score;
    }

    /**
     * @return ArrayCollection
     */
    public function getComment()
    {
        return $this->comments;
    }

    /**
     * @param ArrayCollection $comment
     */
    public function setComment($comments)
    {
        $this->comments = $comments;
    }


    /**
     * Add comment
     *
     * @param \AppBundle\Entity\Comment $comment
     *
     * @return User
     */
    public function addComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(\AppBundle\Entity\Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return ArrayCollection
     */
    public function getCommentsDone()
    {
        return $this->comments_done;
    }

    /**
     * @param ArrayCollection $comments_done
     */
    public function setCommentsDone($comments_done)
    {
        $this->comments_done = $comments_done;
    }

    /**
     * Add commentsDone
     *
     * @param \AppBundle\Entity\Comment $commentsDone
     *
     * @return User
     */
    public function addCommentsDone(\AppBundle\Entity\Comment $commentsDone)
    {
        $this->comments_done[] = $commentsDone;

        return $this;
    }

    /**
     * Remove commentsDone
     *
     * @param \AppBundle\Entity\Comment $commentsDone
     */
    public function removeCommentsDone(\AppBundle\Entity\Comment $commentsDone)
    {
        $this->comments_done->removeElement($commentsDone);
    }


    /**
     * @param User $user
     * @param ExecutionContextInterface $context
     * @Validator\Callback(
     *     groups = {"recharge"}
     * )
     */
    public function recharge(ExecutionContextInterface $context){
        dump('hola');
        if($this->getRecharge() + $this->getBalance() > 1000){

            //$context->addViolation(, array(
            $context->buildViolation('Recharge not allowed. You are exceding your maximum balance. (1000€)')
                ->atPath('recharge')
                ->addViolation();


        }

    }
}
