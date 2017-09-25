<?php

namespace SMS\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\PaymentBundle\Entity\PaymentType;
use API\Form\Type\HiddenEntityType;
use SMS\PaymentBundle\Entity\RegistrationType as TypeRegistration;
use SMS\EstablishmentBundle\Entity\Establishment;

class PaymentTypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];

        $builder
              ->add('typePaymentName' ,TextType::class , array(
                  'label' => 'paymenttype.field.typePaymentName')
              )
              ->add('price' ,TextType::class , array(
                  'label' => 'paymenttype.field.price')
              )
              ->add('registrationFee' ,TextType::class , array(
                  'label' => 'paymenttype.field.registrationFee')
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
            'data_class' => PaymentType::class
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_paymentbundle_paymenttype';
    }


}
