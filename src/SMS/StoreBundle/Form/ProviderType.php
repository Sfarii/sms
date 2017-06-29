<?php

namespace SMS\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use API\Form\Type\HiddenEntityType;
use SMS\EstablishmentBundle\Entity\Establishment;
use SMS\StoreBundle\Entity\Provider;

class ProviderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];
        
        $builder
          ->add('socialReason' ,TextType::class , array(
              'label' => 'provider.field.socialReason')
          )
          ->add('phone' ,TextType::class , array(
              'label' => 'provider.field.phone')
          )
          ->add('address' ,TextType::class , array(
              'label' => 'provider.field.address')
          )
          ->add('fax' ,TextType::class , array(
              'label' => 'provider.field.fax')
          )
          ->add('email' ,TextType::class , array(
              'label' => 'provider.field.email')
          )
          ->add('establishment', HiddenEntityType::class, array(
              'class' => Establishment::class,
              'data' =>  $establishment, // Field value by default
              ))
          ->add('save', SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Provider::class
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_storebundle_provider';
    }


}
