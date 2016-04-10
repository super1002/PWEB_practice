<?php

// src/AppBundle/Controller/AuthenticationController.php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Form\LoginType;
use AppBundle\Entity\User;
use \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder as BCrypt;

class AuthenticationController extends Controller
{

    public function showAction(Request $request)
    {

        $form = $this->createForm(LoginType::class);


        return $this->render('default/login.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function showBarAction(Request $request)
    {

        $form = $this->createForm(LoginType::class);

        return $this->render('default/login_bar.html.twig', array(
            'form' => $form->createView()
        ));
    }
}