<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class AddToBasketForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'required'  => true,
                'label' => false,
                'attr'  => array(
                    'class' => 'stepper',
                    'min' => 0,
                    'max' => 1000,
                    'value' => 1
                )
            ])
        ;
    }
}
