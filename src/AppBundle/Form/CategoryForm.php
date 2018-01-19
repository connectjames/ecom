<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\RelationCategoryProduct;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                'required'  => true,
                'label' => false
            ])
            ->add('sub', TextType::class, [
                'required'  => false,
                'label' => false
            ])
            ->add('description', TextareaType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => array(
                    'class' => 'summernote',
                    'data-height' => 300,
                    'data-lang' => 'en-US'
                )
            ])
            ->add('metaTitle', TextType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => array(
                    'maxlength' => 55
                )
            ])
            ->add('metaDescription', TextType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => array(
                    'maxlength' => 160
                )
            ])
            ->add('metaKeywords', TextType::class, [
                'required'  => false,
                'label' => false
            ])
            ->add('slug', TextType::class, [
                'required'  => false,
                'label' => false
            ])
            ->add('image', FileType::class, [
                'required'  => false,
                'label' => false,
                'mapped' => false
            ])
            ->add('products', CollectionType::class, [
                'entry_type'   => RelationProductForm::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class
        ]);
    }
}
