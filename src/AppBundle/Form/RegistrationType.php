<?php

// src/AppBundle/Form/RegistrationType.php
namespace AppBundle\Form;


use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RegistrationType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, array(
                'attr' => array(
                    'data-validation' => 'custom',
                    'data-validation-regexp' => '^\w{6,}$'
                )
            ))
            ->add('email', EmailType::class, array(
                'attr' => array(
                    'data-validation' => 'email'
                ))
            )
            ->add('plainPassword', RepeatedType::class, array(
                'options' => array('attr' => array(
                    'data-validation' => 'length',
                    'data-validation-length' => '6-10',
                    'data-validation-error-msg-container' => '#errors',
                    'data-validation-error-msg' => 'The password value must be between 6-10 characters',
                    'class' => 'password-field'
                )),
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'required' => true,
                'first_options'  => array('label' => 'Password'),
                'second_options' => array('label' => 'Repeat Password')
            ))
            ->add('twitter_user', TextType::class, array(
                'attr' => array(
                    'data-validation' => 'custom',
                    'data-validation-regexp' => '\@[a-zA-Z0-9_]{1,16}'
                )
            ))
            ->add('file', FileType::class, array(
                'attr' => array(
                    'data-validation' => 'mime size',
                    'data-validation-error-msg-container' => '#image-errors',
                    'data-validation-error-msg' => 'The file must be an image of type JPG, GIF or PNG and not larger than 2MB',
                    'data-validation-max-size' => '2M',
                    'data-validation-allowing' => 'jpg, png, gif',
                    'class' => 'validate'
                ),
                'label' => 'Profile Picture',
                'data_class'=> UploadedFile::class
            ))
            ->add('submit', SubmitType::class)
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

}