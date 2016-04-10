<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 10/4/16
 * Time: 12:39
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class ProfileController extends Controller
{

    const MAX_BALANCE = 1000;
    const PAYPAL = 1;
    const MASTER_CARD = 2;
    const VISA = 3;
    const BITCOIN = 4;

    public function recharge(User $user, ExecutionContextInterface $context){

        if($user->getRecharge() + $user->getBalance() > ProfileController::MAX_BALANCE){
            $context->addViolationAt('recharge', 'Recharge not allowed. You are exceding your maximum balance. (1000€)');
        }

    }

    public function rechargeAction(){

        $form = $this->createFormBuilder($this->getUser())
            ->add('recharge', NumberType::class)
            ->add('submit', SubmitType::class)
            ->add('payment_method', ChoiceType::class, array(
                'choices' => array(
                    'Mastercard' => ProfileController::MASTER_CARD,
                    'Paypal' => ProfileController::PAYPAL,
                    'Visa' => ProfileController::VISA,
                    'Bitcoin' => ProfileController::BITCOIN
                ),
                'multiple' => false,
                'expanded' => true,
                'placeholder' => 'Choose a payment method'


            ))
            ->getForm();


        return $this->renderView('default/recharge.html.twig',
            array(
                'form' => $form->createView()
            ));
    }

}