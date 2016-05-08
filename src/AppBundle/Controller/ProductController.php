<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 15/4/16
 * Time: 12:07
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProductController extends Controller
{

    public function editAction($category, $uuid){

        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->findBy(array('category' => $category,
            'normalizedName' => $uuid));

        if(is_null($product) or empty($product) ){
            throw $this->createNotFoundException();
        }

        if( ! $this->isGranted('EDIT', $product) ){
            throw $this->createAccessDeniedException();
        }

        //preload product into add product form and process data to perform update

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
        $this->getDoctrine()->getEntityManager()->flush();

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
        $this->getUser()->addPurchase($product);
        $this->getDoctrine()->getManager()->flush();
        dump($this->getUser());
        dump(count($this->getUser()->getPurchases()));

        return $this->render('default/buy_result.html.twig', array(
            'product' => $product,
            'historic' => $this->getUser()->getPurchases()
        ));
    }


}