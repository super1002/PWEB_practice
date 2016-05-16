<?php

// src/AppBundle/Controller/AuthenticationController.php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use AppBundle\Form\LoginType;
use AppBundle\Form\LoginBarType;
use AppBundle\Entity\User;
use \Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder as BCrypt;

class AuthenticationController extends Controller
{

    public function showAction(Request $request)
    {

        $form = $this->createForm(LoginType::class);
        $utils = $this->get('security.authentication_utils');
        $error = $utils->getLastAuthenticationError();
        $lastUsername = $utils->getLastUsername();


        return $this->render('default/login.html.twig', array(
            'form' => $form->createView(),
            'lastUsername' => $lastUsername,
            'error' => $error
        ));
    }


    public function showBarAction(Request $request, $target)
    {

        if($target == 'homepage' || $target == 'login'){
            $target = null;
        }
        $form = $this->createForm(LoginBarType::class, null, array(
            'target' => $target
        ));

        return $this->render('default/login_bar.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function recoveryAction(Request $request, $username)
    {

        $repo = $this->getDoctrine()->getRepository('AppBundle:User');
        $user = $repo->loadUserByUsername($username);

        if ($user){

            $status = 1;
            $mailer = $this->get('app.service.mailer.mailer_repository');
            $mailer->sendPasswordRecoveryEmail($user);
        }else{
            $status = 0;
        }

        return $this->render('default/password_recovery.html.twig', array(
            'status' => $status
        ));
    }
}