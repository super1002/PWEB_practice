<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User extends BaseUser
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
     * @Assert\Regex(
     *     pattern = "^@?(\w){1,15}$"
     * )
     * @ORM\Column(name="twitter", type="varchar", length=255)
     */
    protected $twitter_user;

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



}

