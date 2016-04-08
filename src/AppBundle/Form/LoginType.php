<?php

// src/AppBundle/Form/LoginType.php
namespace AppBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class LoginType extends AbstractType{


    protected $router;

    public function __construct(UrlGeneratorInterface  $router)
    {
        $this->router = $router;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->generate('login_check')) //must inject router service for this to work
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->add('_target_path', HiddenType::class, array(
                'data' => '/'
            ))

        ;

    }


    public function getBlockPrefix()
    {
        return 'app_user_login';
    }

}