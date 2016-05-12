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
        $limit = 12;
        $allProducts = $repo->getAllProducts(); //Get all products that did not expire and have stock > 0
        $totalProducts = count($allProducts);
        $maxPages = ceil($totalProducts / $limit);

        if ($page > $maxPages || $page < 1)
        {
            throw $this->createNotFoundException();
        }

        $products = array_chunk($repo->getOrderedByPopular($page, $limit), 3);
        $totalVisits = $repo->countTotalVisits();

        return $this->render('default/popular.html.twig', array(
            'products' => $products,
            'maxPages' => $maxPages,
            'page'     => $page,
            'total'    => $totalVisits
        ));
    }

}
