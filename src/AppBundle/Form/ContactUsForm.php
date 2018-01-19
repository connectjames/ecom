<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class ContactUsForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'required'  => true,
                'label' => false,
            ])
            ->add('fullName', TextType::class, [
                'required'  => true,
                'label' => false,
            ])
            ->add('phone', TextType::class, [
                'required'  => false,
                'label' => false,
                'attr' => array(
                    'pattern' => '^[0-9]*$'
                ),
            ])
            ->add('message', TextareaType::class, [
                'required' => true,
                'label' => false,
            ])
            ->add('captcha', CaptchaType::class);
        ;
    }
}