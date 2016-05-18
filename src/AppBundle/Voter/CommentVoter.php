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

class CommentVoter extends Voter
{

    const VIEW = 'VIEW';
    const EDIT = 'EDIT';

    /**
     * Determines if the attribute and comment are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed $comment The comment to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool True if the attribute and subject are supported, false otherwise
     */
    protected function supports($attribute, $comment)
    {
        if(!($comment instanceof Comment)) return false;

        if(!in_array($attribute, array(self::EDIT, self::VIEW))) return false;

        return true;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     *
     * @param string $attribute
     * @param mixed $comment
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($attribute, $comment, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $comment is a Comment object, thanks to supports


        switch($attribute) {
            case self::VIEW:
                return $this->canView($comment, $user);
            case self::EDIT:
                return $this->canEdit($comment, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView($product, $user)
    {
        //for now all product should be public since there's no restriction for product viewing, creating this method
        //convention
        return true;
    }

    /**
     * @param Comment $comment
     * @param User $user
     * @return bool
     *
     * If the user owns the product he can edit it. Since there is no admin anyone else can edit a product that is not owner of
     * Normal logic would also allow admins to modify products.
     */
    private function canEdit($comment, $user)
    {
        if($comment->getAuthor() === $user) return true;

        return false;
    }
}