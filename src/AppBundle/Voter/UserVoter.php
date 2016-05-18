<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 15/4/16
 * Time: 11:23
 */

namespace AppBundle\Voter;


use AppBundle\Entity\Comment;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{

    const COMMENT = "COMMENT";

    private $doctrine;

    /**
     * Determines if the attribute and comment are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $subject The comment to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */

    public function __construct(\Doctrine\ORM\EntityManager $doctrine){
        $this->doctrine = $doctrine;
    }

    protected function supports($attribute, $subject)
    {
        dump(-1);
        if(!($subject instanceof User)){
            dump(-2);
            return false;
        }

        if(!in_array($attribute, array(self::COMMENT))) return false;

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /**
         * @var User $user
         */
        $user = $token->getUser();

        $user = $this->doctrine->getRepository('AppBundle:User')->find($user->getId());

        /*if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            dump(1);
            return false;
        }*/

        // you know $comment is a Comment object, thanks to supports
        dump(10);

        switch($attribute) {
            case self::COMMENT:
                return $this->canComment($subject, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }


    /**
     * @param User $subject
     * @param User $user
     * @return bool
     *
     * If the user owns the product he can edit it. Since there is no admin anyone else can edit a product that is not owner of
     * Normal logic would also allow admins to modify products.
     */
    private function canComment($subject, $user)
    {
        dump($subject);
        dump($user);

        foreach($user->getPurchases() as $purchase){
            if($purchase->getBuyer() == $subject){
                foreach($subject->getComments() as $comment){
                    if($comment->getAuthor() == $user){
                        dump(2);
                        return false;
                    }
                }
                dump(3);
                return true;
            }
        }
        dump(4);
        return false;
    }
}