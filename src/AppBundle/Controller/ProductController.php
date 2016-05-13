<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 15/4/16
 * Time: 12:07
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Purchase;
use AppBundle\Form\NewProductType;
use AppBundle\Form\SearchBarType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{

    public function editAction($category, $uuid, Request $request){

        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->findOneBy(array('category' => $category,
            'normalizedName' => $uuid));

        if(is_null($product) or empty($product) ){
            throw $this->createNotFoundException();
        }

        if( ! $this->isGranted('EDIT', $product) ){
            throw $this->createAccessDeniedException();
        }

        //preload product into add product form and process data to perform update
        $form = $this->createForm(NewProductType::class, $product);

        // ???
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $resultset = $this->getDoctrine()->getRepository('AppBundle:Product')->findBy(array('name' => $product->getName()));
            dump($resultset);
            if(is_null($resultset) or empty($resultset)){
                $product->setNormalizedName(join('-', preg_split('/\s/', strtolower($product->getName()))));
            }else{
                $product->setNormalizedName($resultset[0]->getNormalizedName() . '-' . count($resultset));
            }
            $product->setCreationDate(new \DateTime());

            $file = $product->getFile();
            if (null !== $file) {

                $extension =  $file->guessExtension();
                if (!$extension) {
                    // extension cannot be guessed
                    $extension = 'bin';
                }

                $fileName = $product->getNormalizedName().'.400.'.$extension;

                $productPicturesDir = $this->getParameter('kernel.root_dir').'/../web/uploads/Products/'.$product->getOwner()->getUsername().'/';

                //Getting the file saved

                $file->move($productPicturesDir, $fileName);
                $product->setPicture('uploads/Products/'.$product->getOwner()->getUsername().'/'. $fileName);
            }
        }

        //when submited and all done
        //$this->getDoctrine()->getManager('AppBundle:Product')->flush();

        return $this->redirectToRoute('homepage');
    }

    public function deleteAction($category, $uuid){

        if($this->getDoctrine()->getRepository('AppBundle:Product')->remove($category, $uuid)){
            $options = array('status' => 1);
        }else{
            $options = array('status' => 0);
        }
        return $this->redirectToRoute('delete_success', $options);
    }

    public function showAction($category, $uuid){

        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->findOneBy(array('category' => $category, 'normalizedName' => $uuid));

        if( is_null($product) ){
            //check if has been moved
            $target = $this->getDoctrine()->getRepository('AppBundle:Redirection')->find($category . '/' . $uuid);
            if( is_null($target) ){
                throw $this->createNotFoundException();
            }else{
                return $this->redirect($target->getDestination());
            }
        }

        if( $product->isNotAvailable() ){
            throw $this->createNotFoundException();
        }

        $product->setNumvisits($product->getNumvisits() + 1);
        $this->getDoctrine()->getManager()->flush();

        return $this->render('default/product.html.twig', array(
            'product' => $product
        ));

    }

    public function buyAction($category, $uuid){

        $product = $this->getDoctrine()->getRepository('AppBundle:Product')
            ->findOneBy( array('category' => $category, 'normalizedName' => $uuid));

        if( is_null($product) or $product->isNotAvailable()){
            throw $this->createNotFoundException();
        }

        if( $this->getUser()->getBalance() < $product->getPrice() ){
            $this->addFlash('modal', 'You do not have enough money to purchase this product. Recharge your account\'s
            balance and proceed again with the purchase');
            return $this->redirectToRoute('product_show', array('category' => $category, 'uuid' => $uuid));
        }

        $this->getUser()->setBalance($this->getUser()->getBalance() - $product->getPrice());
        $product->getOwner()->setBalance($product->getPrice() + $product->getOwner()->getBalance());
        $product->setStock($product->getStock() - 1);
        $purchase = new Purchase();
        $purchase->setBuyer($this->getUser());
        $purchase->setProduct($product);
        $em = $this->getDoctrine()->getManager();
        $em->persist($purchase);
        $em->flush();

        return $this->render('default/buy_result.html.twig', array(
            'product' => $product,
            'historic' => $this->getUser()->getPurchases()
        ));
    }

    public function searchAction(){

        $form = $this->createForm(SearchBarType::class, null);

        return $this->render('default/search_bar.html.twig', array(
            'form' => $form->createView()
        ));

    }


}