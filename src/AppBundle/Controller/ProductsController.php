<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\SearchBarType;
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

    public function searchResultsAction(Request $request, $page){

        $form = $this->createForm(SearchBarType::class);

        $form->handleRequest($request);

        $results = null;
        $pages = 1;
        $string = '';

        if($page < 1){

            if(($form->isValid() and $form->isSubmitted())){
                $this->container->get('session')->getFlashbag()->set('search_string', $form->get('search')->getData());
            }else if ($this->container->get('session')->getFlashbag()->has('search_string')[0]){
                $this->container->get('session')->getFlashbag()->set('search_string', $this->container->get('session')
                    ->getFlashbag()->get('search_string')[0]);
            }

            return $this->redirectToRoute('search', array('page' => 1));
        }

        if( ($form->isValid() and $form->isSubmitted())){
            $string = $form->get('search')->getData();
            $resultset = $this->getDoctrine()->getRepository('AppBundle:Product')->searchAll($string, $page);
            $results = $resultset[0];
            $pages = $resultset[1];
            $this->container->get('session')->getFlashbag()->set('search_string', $string);

        }else if ($this->container->get('session')->getFlashbag()->has('search_string')){
            $string = $this->container->get('session')->getFlashbag()->get('search_string')[0];
            $resultset = $this->getDoctrine()->getRepository('AppBundle:Product')->searchAll($string, $page);
            $results = $resultset[0];
            $pages = $resultset[1];
            $this->container->get('session')->getFlashbag()->set('search_string', $string);
        }

        if($results == null){

            if(($form->isValid() and $form->isSubmitted())){
                $this->container->get('session')->getFlashbag()->set('search_string', $form->get('search')->getData());
            }else if ($this->container->get('session')->getFlashbag()->has('search_string')[0]){
                $this->container->get('session')->getFlashbag()->set('search_string', $this->container->get('session')
                    ->getFlashbag()->get('search_string')[0]);
            }

            return $this->redirectToRoute('search', array('page' => $pages));
        }

        return $this->render('default/search_result.html.twig', array(
            'string' => $string,
            'results' => $results,
            'pages' => $pages,
            'page' => $page
        ));
    }

}
