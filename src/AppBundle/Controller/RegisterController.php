<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ConfirmationToken;
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

            /**
             * @var BCrypt $encoder
             */
            $encoder = $this->get('security.password_encoder');

            $user->setPassword($encoder->encodePassword($user, $user->getSalt()));
            $user->eraseCredentials();
            //make csrf check. no idea how that's done.


            //generate a confirmationToken
            $token = new ConfirmationToken($user->getEmail(), $user->generateConfirmationToken());
            $em->persist($token);

            //store the user information on the database
            //persist the confirmation token mapped to the email
            $em->flush();

            //send the email with the confirmation url
            /**
             * MailerRepository $mailer
             */
            $mailer = $this->get('mailer_repo');
            $mailer->sendConfirmationEmailMessage($user, $token->getToken());

            //Redirigim perque no puguin fer refresh i "reenviar" les dades.
            return $this->redirectToRoute('registration_success');
        }

        return $this->render('default/registration.html.twig', array(
            'form' => $form->createView()
        ));
    }
}