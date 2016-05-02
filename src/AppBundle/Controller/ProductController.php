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
use Symfony\Component\Validator\Constraints\DateTime;

class ProductController extends Controller
{

    public function editAction(){

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