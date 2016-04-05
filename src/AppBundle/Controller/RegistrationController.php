<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\RegistrationType;
use AppBundle\Entity\User;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\GetResponseUserEvent;

class RegistrationController extends BaseController
{
    public function registerAction(Request $request)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){


            //Redirigim perque no puguin fer refresh i "reenviar" les dades
            return $this->redirectToRoute('registration_succes');
        }

        return $this->render('default/registration.html.twig', array(
            'form' => $form->createView()
        ));
    }
}