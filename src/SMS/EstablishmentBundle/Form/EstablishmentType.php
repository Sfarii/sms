<?php

namespace SMS\EstablishmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\EstablishmentBundle\Entity\Establishment;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EstablishmentType extends AbstractType
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
                      'label' => false )
                  )
              ->add('establishmentName' ,TextType::class , array(
                  'label' => 'establishment.field.establishmentName')
              )
              ->add('phone' ,TextType::class , array(
                  'label' => 'establishment.field.phone')
              )
              ->add('email' ,TextType::class , array(
                  'label' => 'establishment.field.email')
              )
              ->add('address' ,TextType::class , array(
                  'label' => 'establishment.field.address')
              )
              ->add('theme' ,TextType::class , array(
                  'label' => 'establishment.field.theme' ,
                  'attr' => ['class' => 'theme'])
              )
              ->add('save', SubmitType::class, array(
                  'label' => 'md-fab')
              );

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Establishment::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_schoolbundle_establishment';
    }


}
