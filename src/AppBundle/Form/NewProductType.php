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
            ->add('name', TextType::class)
            ->add('description', FroalaEditorType::class)
            ->add('price', NumberType::class)
            ->add('stock', IntegerType::class)
            ->add('expiringDate', DateType::class, array(
                'format' => 'dd MMMM, yyyy',
                'widget' => 'single_text'))
            ->add('file', FileType::class, array('label' => 'Product Picture', 'data_class'=> UploadedFile::class, 'required' => false))
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