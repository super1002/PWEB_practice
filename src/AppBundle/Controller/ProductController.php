<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 15/4/16
 * Time: 12:07
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Comment;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\DateTime;

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
        $this->get

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

        return $this->render('default/product.html.twig', array(
            'product' => $product
        ));

    }


}