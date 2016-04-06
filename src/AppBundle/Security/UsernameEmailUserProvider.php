<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 5/4/16
 * Time: 20:08
 */

namespace AppBundle\Security\User;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UsernameEmailUserProvider extends EntityRepository implements UserProviderInterface
{
    public function loadUserByUsername($username) {
        $retvar = $this->getEntityManager()
            ->createQuery('SELECT u FROM
         AppBundle:User u
         WHERE u.username = :username
         OR u.email = :username')
            ->setParameters(array(
                'username' => $username
            ))
            ->getOneOrNullResult();


        if ($retvar) {

            return $retvar;
        }

        throw new UsernameNotFoundException(
            sprintf('Username/email "%s" does not exist.', $username)
        );
    }

    public function refreshUser(UserInterface $user) {

        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class) {
        return $class === 'AppBundle\Entity\User';
    }
}