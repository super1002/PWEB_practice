<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\RegistrationType;
use AppBundle\Entity\User;
use \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder as BCrypt;


class RegisterController extends Controller
{
    public function showAction(Request $request)
    {
        $user = new User();
        $em = $this->getDoctrine()->getManager();

        $em->persist($user);

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //make csrf check. no idea how that's done.

            //store the user information on the database
            $em->flush();
            //generate a confirmationToken
            /**
             * @var BCrypt $encoder
             */
            $encoder = $this->get('security.password_encoder');
            $token = $encoder->encodePassword($user->getEmail(), 'confirmation');
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