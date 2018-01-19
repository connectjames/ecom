<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use AppBundle\Entity\Dropshipper;
use AppBundle\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('name', TextType::class, [
                'required'  => true,
                'label' => false,
                'attr'  => array(
                    'maxlength' => 100
                )
            ])
            ->add('sku', TextType::class, [
                'required'  => true,
                'label' => false
            ])
            ->add('price', TextType::class, [
                'required'  => true,
                'label' => false
            ])
            ->add('weight', IntegerType::class, [
                'required'  => true,
                'label' => false
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
            ->add('videoTitle', TextType::class, [
                'required'  => false,
                'label' => false
            ])
            ->add('videoLink', TextType::class, [
                'required'  => false,
                'label' => false
            ])
            ->add('capacityTable', TextareaType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => array(
                    'maxlength' => 255
                )
            ])
            ->add('contentsTable', TextareaType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => array(
                    'maxlength' => 255
                )
            ])
            ->add('productCodeTable', TextareaType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => array(
                    'maxlength' => 255
                )
            ])
            ->add('weightTable', TextareaType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => array(
                    'maxlength' => 255
                )
            ])
            ->add('dimensionTable', TextareaType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => array(
                    'maxlength' => 255
                )
            ])
            ->add('descriptionTable', TextareaType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => array(
                    'maxlength' => 255
                )
            ])
            ->add('packQuantityTable', TextareaType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => array(
                    'maxlength' => 255
                )
            ])
            ->add('image', FileType::class, [
                'required'  => false,
                'label' => false,
                'mapped' => false
            ])
            ->add('shortDescription', TextareaType::class, [
                'required'  => false,
                'label' => false,
                'attr'  => array(
                    'class' => 'summernote',
                    'data-height' => 100,
                    'data-lang' => 'en-US'
                )
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
            ->add('dropshipper', EntityType::class, [
                'class' => Dropshipper::class,
                'expanded' => false,
                'label' => false,
                'choice_label' => 'name',
                'required'  => true
            ])
            ->add('categories', CollectionType::class, [
                'entry_type' => RelationCategoryForm::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('parentProducts', CollectionType::class, [
                'entry_type' => RelationChildProductForm::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class
        ]);
    }
}
