<?php

namespace SMS\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use SMS\PaymentBundle\Entity\Registration;
use SMS\PaymentBundle\Entity\PaymentType as TypePayment;
use Doctrine\ORM\EntityManager;
use API\Form\Type\HiddenEntityType;
use API\Form\Type\MonthType;
use SMS\EstablishmentBundle\Entity\Establishment;

class RegistrationType extends AbstractType
{
    /**
     * @var String Class Names
     */
    protected $establishmentClass;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct( $establishmentClass)
    {
        $this->establishmentClass =  $establishmentClass;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $establishment = $options['establishment'];

        $builder
            ->add('paymentType' , EntityType::class , array(
                'class' => TypePayment::class ,
                'property' => "TypePaymentName",
                'query_builder' => function ( $er) use ($establishment) {
                    return $er->createQueryBuilder('paymentType')
                              ->join('paymentType.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'placeholder'   => 'payment.field.select_paymentType',
                'label' => 'payment.field.paymentType',
                'attr'          => [ 'class'=> 'paymentTypeField'])
            )
            ->add('registration', CheckboxType::class, array(
              'label' => 'registration.field.registered',
                )
            );

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_paymentbundle_registration';
    }


}
