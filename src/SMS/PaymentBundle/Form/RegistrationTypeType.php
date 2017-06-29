<?php

namespace SMS\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\PaymentBundle\Entity\RegistrationType;
use API\Form\Type\HiddenEntityType;
use SMS\EstablishmentBundle\Entity\Establishment;

class RegistrationTypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];

        $builder
              ->add('registrationTypeName' ,TextType::class , array(
                  'label' => 'registrationtype.field.registrationTypeName')
              )
              ->add('registrationFee' ,TextType::class , array(
                  'label' => 'registrationtype.field.registrationFee')
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
            'data_class' => RegistrationType::class
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_paymentbundle_registrationtype';
    }


}
