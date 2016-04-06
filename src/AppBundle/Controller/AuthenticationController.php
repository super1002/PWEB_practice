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
    /**
     * @Route("/login", name="login")
     */
    public function showAction(Request $request)
    {

        $form = $this->createForm(LoginType::class);

        if($form->isSubmitted() && $form->isValid()){

            //make csrf check. no idea how that's done.

            //store the user information on the database

            //generate a confirmationToken

            //persist the confirmation token mapped to the user

            //send the email with the confirmation url

            //Redirigim perque no puguin fer refresh i "reenviar" les dades.
            return $this->redirectToRoute('Authentication_success');
        }

        return $this->render('default/login.html.twig', array(
            'form' => $form->createView()
        ));
    }
}