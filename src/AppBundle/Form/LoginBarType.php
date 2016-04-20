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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class LoginBarType extends AbstractType{


    protected $router;

    public function __construct(UrlGeneratorInterface  $router)
    {
        $this->router = $router;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if($options['target'] == 'homepage'){
            $options['target'] = '/';
        }
        $builder
            ->setAction($this->router->generate('login_check'))
            ->add('username', TextType::class)
            ->add('password', PasswordType::class)
            ->add('submit', SubmitType::class)
            ->add('_target_path', HiddenType::class, array(
                'data' => $options['target']
            ))

        ;

        dump($options['target']);

    }


    public function configureOptions(OptionsResolver $resolver)
    {
        AbstractType::configureOptions($resolver);
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            // a unique key to help generate the secret token
            'csrf_token_id'   => 'authenticate',
            'target'          => '/'
        ));
    }


    public function getBlockPrefix()
    {
        return 'app_user_login';
    }

}