<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ConfirmationToken
 *
 * @ORM\Table(name="confirmation_token")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ConfirmationTokenRepository")
 */
class ConfirmationToken
{

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=60)
     */
    private $email;

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(name="token", type="string", length=255, unique=true)
     */
    private $token;


    public  function __construct($email, $token)
    {

        $this->email = $email;
        $this->token = $token;

    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return ConfirmationToken
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return ConfirmationToken
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
