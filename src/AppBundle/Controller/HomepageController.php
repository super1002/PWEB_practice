<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Product;


class HomepageController extends Controller
{

    public function showAction(Request $request)
    {

        $repo = $this->getDoctrine()->getRepository('AppBundle:Product');

        $popularProducts = array_chunk($repo->getMostViewedProducts(), 3);
        $newestProducts = $repo->getNewestProducts();
        $newestProduct = $newestProducts[0];
        $newestProducts = array_chunk(array_slice($newestProducts, 1), 3);

        return $this->render('default/index.html.twig', array(
            'popularProducts' => $popularProducts,
            'newestProduct' => $newestProduct,
            'newestProducts' => $newestProducts
        ));
    }
}
