<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;
use Doctrine\ORM\Tools\Pagination\Paginator;



class ProductsController extends Controller
{

    public function showPopularAction(Request $request, $page)
    {

        $repo = $this->getDoctrine()->getRepository('AppBundle:Product');
        $limit = 10;
        $totalProducts = count($repo->getAllProducts()); // Count of ALL products
        $maxPages = ceil($totalProducts / $limit);

        if ($page > $maxPages || $page < 1)
        {
            throw $this->createNotFoundException();
        }

        //$prova = $repo->getOrderedByPopular($page, $limit);
        //dump($prova);
        $products = array_chunk($repo->getOrderedByPopular($page, $limit), 3);


        return $this->render('default/popular.html.twig', array(
            'products' => $products,
            'maxPages' => $maxPages,
            'page'     => $page,
        ));
    }
}
