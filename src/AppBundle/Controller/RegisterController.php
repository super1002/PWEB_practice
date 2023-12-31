<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ConfirmationToken;
use AppBundle\Repository\ConfirmationTokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\RegistrationType;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder as BCrypt;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;


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
            $factory = $this->get('security.encoder_factory');
            $encoder = $factory->getEncoder($user);

            $user->setPassword($encoder->encodePassword($user->getPlainPassword(), $user->getSalt()));
            //$user->eraseCredentials();
            //make csrf check. no idea how that's done.

            /** @var UploadedFile $file */
            $file = $user->getFile();
            if (null !== $file) {

                $extension =  $file->guessExtension();
                if (!$extension) {
                    // extension cannot be guessed
                    $extension = 'bin';
                }

                $fileName = $user->getUsername().'.'.$extension;

                $profilePicturesDir = $this->getParameter('kernel.root_dir').'/../web/uploads/ProfilePictures/';

                //Getting the file saved

                $file->move($profilePicturesDir, $fileName);
                $user->setProfilePicture('uploads/ProfilePictures/'. $fileName);

            }

            //generate a confirmationToken
            $token = new ConfirmationToken($user->getEmail(), $user->generateConfirmationToken());
            $em->persist($token);

            //store the user information on the database
            //persist the confirmation token mapped to the email
            $em->flush();

            dump($user);

            //send the email with the confirmation url
            /**
             * MailerRepository $mailer
             */
            $mailer = $this->get('app.service.mailer.mailer_repository');
            $mailer->sendConfirmationEmailMessage($user, $token->getToken());

            //Redirigim perque no puguin fer refresh i "reenviar" les dades.
            $this->container->get('session')->getFlashbag()->set('user_id', $user->getId());
            return $this->redirectToRoute('registration_success');
        }

        return $this->render('default/registration.html.twig', array(
            'form' => $form->createView()
        ));
    }


    public function confirmAction(Request $request,$token){

        //check the token against database.
        $repo = $this->getDoctrine()->getRepository('AppBundle:ConfirmationToken');
        $conf_token = $repo->find($token);

        if($conf_token){
            $em = $this->getDoctrine()->getManager();
            //activamos el usuario a traves del email
            $user_repo = $this->getDoctrine()->getRepository('AppBundle:User');

            $user = $user_repo->findOneBy(array('email'=> $conf_token->getEmail()));
            if (!$user) {
                throw new UsernameNotFoundException("User not found");
            } else {
                $user->setIsEnabled(true);
                $token = new UsernamePasswordToken($user, null, "app.username_email_user_provider", $user->getRoles());
                $this->get("security.token_storage")->setToken($token); //now the user is logged in

                //now dispatch the login event
                $event = new InteractiveLoginEvent($request, $token);
                $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);
            }

            $em->remove($conf_token);
            $em->flush();
            $this->addFlash('modal', 'Welcome to KingPong. Your account has been succesfully activated.');
            return $this->redirectToRoute('homepage');


        }else{

            throw $this->createNotFoundException();
            //return $this->redirectToRoute('invalid_token');
        }

    }

    public function successAction(Request $request){

        $ru = $this->container->get('session')->getFlashbag()->get('user_id')[0];
        $this->container->get('session')->getFlashbag()->set('registered_user', $ru);

        return $this->render('default/register_success.html.twig');
    }


    public function resendEmailAction(Request $request){

        $u_id = $this->container->get('session')->getFlashbag()->get('registered_user')[0];
        $user = $this->getDoctrine()->getRepository('AppBundle:User')->find($u_id);
        //generate a confirmationToken
        $token = $this->getDoctrine()->getRepository('AppBundle:ConfirmationToken')->findOneBy(array('email' => $user->getEmail()));
        $token->setToken( $user->generateConfirmationToken() );
        //store the user information on the database
        //persist the confirmation token mapped to the email
        $this->getDoctrine()->getManager()->flush();

        //send the email with the confirmation url
        /**
         * MailerRepository $mailer
         */
        $mailer = $this->get('app.service.mailer.mailer_repository');
        $mailer->sendConfirmationEmailMessage($user, $token->getToken());
        $this->container->get('session')->getFlashbag()->set('user_id', $user->getId());
        $email = $user->getEmail();
        $this->container->get('session')->getFlashbag()->set('modal', "The email has been succesfully re-sent to $email.");

        return $this->redirectToRoute('registration_success');
    }

}