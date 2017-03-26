<?php

namespace SMS\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\UserBundle\Entity\Administrator;
use Vich\UploaderBundle\Form\Type\VichImageType;
use SMS\UserBundle\Form\Type\GenderType;
use SMS\UserBundle\Form\EventSubscriber\AddUsernamePasswordFieldListener;

class AdministratorType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile',  VichImageType::class, array(
                    'allow_delete' => false, // not mandatory, default is true
                    'download_link' => false, // not mandatory, default is true
                    'label' => false ,
                    'attr' => [ 'form.grid'=> "none"])
                )
            ->add('firstName' ,TextType::class , array(
                'label' => 'administrator.field.firstName')
            )
            ->add('lastName' ,TextType::class , array(
                'label' => 'administrator.field.lastName')
            )
            ->add('gender' ,GenderType::class , array(
                'label' => false)
            )
            ->add('phone' ,TextType::class , array(
                'label' => 'administrator.field.phone')
            )
            ->add('address' ,TextType::class , array(
                'label' => 'administrator.field.address')
            )
            ->add('email' ,TextType::class , array(
                'label' => 'administrator.field.email')
            )
            ->add('show_username_password', CheckboxType::class, array(
                'label' => 'user.field.show_username_password',
                'mapped' => false
                )
            )
            ->addEventSubscriber(new AddUsernamePasswordFieldListener())
            
            ->add('save', SubmitType::class ,array(
                'validation_groups' => "SimpleRegistration",
            ));

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Administrator::class,
            'allow_extra_fields' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_userbundle_administrator';
    }


}
