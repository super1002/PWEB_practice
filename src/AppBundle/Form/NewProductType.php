<?php

// src/AppBundle/Form/RegistrationType.php
namespace AppBundle\Form;

use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;


class NewProductType extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'attr' => array(
                    'data-validation' => 'length',
                    'data-validation-length' => 'max50'
                )
            ))
            ->add('description', FroalaEditorType::class)
            ->add('price', NumberType::class, array(
                'attr' => array(
                    'data-validation' => 'number',
                    'data-validation-allowing' => 'range[0.01;1000000000],float'
                )
            ))
            ->add('stock', IntegerType::class, array(
                'attr' => array(
                    'data-validation' => 'number',
                    'data-validation-allowing' => 'range[1;1000000000]'
                )
            ))
            ->add('expiringDate', DateType::class, array(
                'format' => 'dd MMMM, yyyy',
                'widget' => 'single_text'))
            ->add('file', FileType::class, array(
                'label' => 'Product Picture', 'data_class'=> UploadedFile::class,
                'attr' => array(
                    'data-validation' => 'mime size',
                    'data-validation-error-msg-container' => '#image-errors',
                    'data-validation-error-msg' => 'The file must be an image of type JPG, GIF or PNG and not larger than 2MB',
                    'data-validation-max-size' => '2M',
                    'data-validation-allowing' => 'jpg, png, gif',
                    'class' => 'validate'
                )
            ))
            ->add('category', ChoiceType::class, array(
                'choices'  => array(
                    'Table' => 'table',
                    'Blade' => 'blade',
                    'Rubber' => 'rubber',
                    'Ball' => 'ball',
                    'Clothing' => 'clothing',
                    'Other' => 'other',
                )))
            ->add('submit', SubmitType::class)
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Product',
        ));
    }

}