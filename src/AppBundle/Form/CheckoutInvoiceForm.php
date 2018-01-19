<?php

namespace AppBundle\Form;

use AppBundle\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CheckoutInvoiceForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => false
            ])
            ->add('firstName', TextType::class, [
                'required' => true,
                'label' => false
            ])
            ->add('lastName', TextType::class, [
                'required' => true,
                'label' => false
            ])
            ->add('company', TextType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false
            ])
            ->add('address1', TextType::class, [
                'required' => true,
                'label' => false,
                'mapped' => false,
                'attr' => array(
                    'placeholder' => 'First line of address*'
                )
            ])
            ->add('address2', TextType::class, [
                'required' => false,
                'label' => false,
                'mapped' => false,
                'attr' => array(
                    'placeholder' => 'Second line of address'
                )
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'label' => false,
                'mapped' => false
            ])
            ->add('postcode', TextType::class, [
                'required' => true,
                'label' => false,
                'mapped' => false,
                'attr' => array(
                    'class' => 'uppercase',
                    'minlength' => 6,
                    'maxlength' => 8
                )
            ])
            ->add('phone', TextType::class, [
                'required'  => true,
                'label' => false,
                'mapped' => false,
                'attr' => array(
                    'pattern' => '[0-9]*'
                )
            ])
            ->add('purchaseOrderNumber', TextType::class, [
                'required' => false,
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
            'validation_groups' => ['Default', 'Registration'],
        ]);
    }
}
