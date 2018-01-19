<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class InvoiceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'required'  => true
            ])
            ->add('lastName', TextType::class, [
                'required'  => true
            ])
            ->add('email', EmailType::class, [
                'required'  => true
            ])
            ->add('company', TextType::class, [
                'required'  => false
            ])
            ->add('address1', TextType::class, [
                'required'  => true
            ])
            ->add('address2', TextType::class, [
                'required'  => false
            ])
            ->add('city', TextType::class, [
                'required'  => true
            ])
            ->add('postcode', TextType::class, [
                'required'  => true
            ])
            ->add('phone', IntegerType::class, [
                'required'  => true
            ])
        ;
    }
}
