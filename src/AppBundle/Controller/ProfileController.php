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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
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


        $productPicturesTemp = null;
        $product = new Product();

        $em = $this->getDoctrine()->getManager();

        $em->persist($product);

        $form = $this->createForm(NewProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setOwner($this->getUser());

            $repo = $this->getDoctrine()->getRepository('AppBundle:Profile');
            $resultset = $repo->findBy(array('name' => $product->getName()));
            if(is_null($resultset) or empty($resultset)){
                $product->setNormalizedName($product->getName());
            }else{
                $product->setNormalizedName($product->getNormalizedName() . count($resultset));
            }

            $file = $product->getFile();
            if (null !== $file) {

                $extension =  $file->guessExtension();
                if (!$extension) {
                    // extension cannot be guessed
                    $extension = 'bin';
                }

                $fileName = $product->getNormalizedName().'.'.$extension;

                $productPicturesDir = $this->getParameter('kernel.root_dir').'/../web/uploads/Products/'.$product->getOwner()->getUsername().'/';

                //Getting the file saved

                $file->move($productPicturesDir, $fileName);
                $product->setPicture('uploads/Products/'.$product->getOwner()->getUsername().'/'. $fileName);

                $em->flush();

                $this->addFlash('modal','Your product was added');
                return $this->redirectToRoute('homepage');
            }
            else{

                $filesystem = new Filesystem();

                if($filesystem->exists('web/uploads/Products/TEMP/'.$this->getUser().'.'.'temp')){

                    $extension =  $file->guessExtension();
                    if (!$extension) {
                        // extension cannot be guessed
                        $extension = 'bin';
                    }

                    $filesystem->rename('web/uploads/Products/TEMP/'.$this->getUser().'.'.'temp', 'uploads/Products/'.$product->getOwner()->getUsername().'/'. $product->getNormalizedName().'.'.$extension);

                    $productPicturesDir = $this->getParameter('kernel.root_dir').'/../web/uploads/Products/'.$product->getOwner()->getUsername().'/';

                    //Getting the file saved

                    $file->move($productPicturesDir, $product->getNormalizedName().'.'.$extension);
                    $product->setPicture('uploads/Products/'.$product->getOwner()->getUsername().'/'. $fileName);

                    $em->flush();

                    $this->addFlash('modal','Your product was added');
                    return $this->redirectToRoute('homepage');
                }
            }
            //HUE
        }
        else{
            $file = $product->getFile();

            if (null !== $file) {

                $fileName = $this->getUser().'.'.'temp';

                $productPicturesTemp = $this->getParameter('kernel.root_dir').'/../web/uploads/Products/TEMP/';

                //Getting the file saved

                $file->move($productPicturesTemp, $fileName);
            }
        }

        return $this->render('default/new_product.html.twig',
            array(
                'form' => $form->createView(),
                'image' => $productPicturesTemp
            ));
    }


    //for the addproductaction add an extra field on the form that consists of a choice field that allows to select the
    //different kind of products that can be inserted and that will be used to generate the SEO like url for the products.
    //the available options are: "tables | blades | balls | rubbers | clothing | other" if new are added notify in order
    //to add them to the routing configuration.

}