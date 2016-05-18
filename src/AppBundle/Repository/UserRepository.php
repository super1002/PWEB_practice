<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 5/4/16
 * Time: 20:08
 */

namespace AppBundle\Repository;

use AppBundle\Entity;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserRepository extends EntityRepository implements UserLoaderInterface, UserProviderInterface
{
    public function loadUserByUsername($username) {

        $retvar = $this->createQueryBuilder('u')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->where('u.username = :username OR u.email = :email')
            ->getQuery()
            ->getOneOrNullResult();



        if ($retvar) {

            return $retvar;
        }

        throw new UsernameNotFoundException(
            sprintf('Username/email "%s" does not exist.', $username)
        );
    }

    public function refreshUser(UserInterface $user) {

        if (!($user instanceof User)) {
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