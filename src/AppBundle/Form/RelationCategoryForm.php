<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Entity\RelationCategoryProduct;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelationCategoryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => false
            ])
            ->add('position', IntegerType::class, [
                'required'  => true,
                'label' => false,
                'data' => 1,
                'attr' => array(
                    'class' => 'display-none'
                )
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RelationCategoryProduct::class
        ]);
    }
}
