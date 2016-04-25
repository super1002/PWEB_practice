<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 10/4/16
 * Time: 12:39
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\NewProductType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Validator;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{

    const MAX_BALANCE = 1000;
    const PAYPAL = 1;
    const MASTER_CARD = 2;
    const VISA = 3;
    const BITCOIN = 4;



    public function rechargeAction(Request $request){

        $form = $this->createFormBuilder($this->getUser(), array(
                'validation_groups' => array('recharge')
            ))
            ->add('recharge', NumberType::class)

            ->add('recharge', NumberType::class, array(
                'label' => 'Quantity (€)',
            ))
            ->add('submit', SubmitType::class)
            ->add('payment_method', ChoiceType::class, array(
                'choices' => array(
                    'Mastercard' => ProfileController::MASTER_CARD,
                    'Paypal' => ProfileController::PAYPAL,
                    'Visa' => ProfileController::VISA,
                    'Bitcoin' => ProfileController::BITCOIN
                ),
                /*'choice_label' => function($value, $key, $index){

                    switch($value){
                        case ProfileController::PAYPAL:
                            $file ='/images/form_payment_method_1.png';
                            break;
                        case ProfileController::MASTER_CARD:
                            $file ='/images/form_payment_method_0.png';
                            break;
                        case ProfileController::VISA:
                            $file ='/images/form_payment_method_2.png';
                            break;
                        case ProfileController::BITCOIN:
                            $file ='/images/icon_bitcoin.ong';
                            break;
                    }

                    return new File($file);
                },*/
                "mapped" => false,
                'multiple' => false,
                'expanded' => true,
                'label' => ' ',


            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getUser()->setBalance($this->getUser()->getBalance()+$this->getUser()->getRecharge());
            $this->getDoctrine()->getManager()->flush();
        }


        return $this->render('default/recharge.html.twig',
            array(
                'form' => $form->createView()
            ));
    }

    public function addProductAction(Request $request){

        $product = new Product();

        $form = $this->createForm(NewProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //HUE
        }

        return $this->render('default/new_product.html.twig',
            array(
                'form' => $form->createView()
            ));
    }


    //for the addproductaction add an extra field on the form that consists of a choice field that allows to select the
    //different kind of products that can be inserted and that will be used to generate the SEO like url for the products.
    //the available options are: "tables | blades | balls | rubbers | clothing | other" if new are added notify in order
    //to add them to the routing configuration.

}