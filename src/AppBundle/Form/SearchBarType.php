<?php

// src/AppBundle/Form/LoginType.php
namespace AppBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;



class SearchBarType extends AbstractType{


    protected $router;

    public function __construct(UrlGeneratorInterface  $router)
    {
        $this->router = $router;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($this->router->generate('search'))
            ->add('search', SearchType::class)
            ->add('submit', SubmitType::class)
        ;

    }

}