<?php

namespace AppBundle\Form;

use AppBundle\Entity\Product;
use AppBundle\Entity\RelationInBetweenProduct;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelationParentProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('parentProduct', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'name',
                'label' => false
            ])
            ->add('position', IntegerType::class, [
                'required'  => true,
                'label' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RelationInBetweenProduct::class
        ]);
    }
}
