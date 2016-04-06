<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\RegistrationType;
use AppBundle\Entity\User;


class RegistrationController extends BaseController
{
    public function registerAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //make csrf check. no idea how that's done.

            //store the user information on the database

            //generate a confirmationToken

            //persist the confirmation token mapped to the user

            //send the email with the confirmation url

            //Redirigim perque no puguin fer refresh i "reenviar" les dades.
            return $this->redirectToRoute('registration_success');
        }

        return $this->render('default/registration.html.twig', array(
            'form' => $form->createView()
        ));
    }
}