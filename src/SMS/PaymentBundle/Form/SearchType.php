<?php

namespace SMS\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use SMS\PaymentBundle\Entity\PaymentType as TypePayment;
use Doctrine\ORM\EntityManager;
use API\Form\Type\HiddenEntityType;
use API\Form\Type\MonthType;
use SMS\EstablishmentBundle\Entity\Establishment;

class SearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $establishment = $options['establishment'];

        $builder
            ->add('textField' ,TextType::class , array(
                  'label' => 'search.student.by_every_thing')
              )
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
                'label' => 'payment.field.paymentType')
            )
            ->add('months' ,MonthType::class , array(
                  'label' => 'paymenttype.field.months',
                  'placeholder'   => 'paymenttype.field.select_months')
              )
            ->add('paid', CheckboxType::class, array(
              'label' => 'search.payment.paid',
                )
            )
            ->add('notPaid', CheckboxType::class, array(
              'label' => 'search.payment.not_paid',
                )
            )
            ->add('hasCredit', CheckboxType::class, array(
              'label' => 'search.payment.has_credit',
                )
            )
            ->add('Registred', CheckboxType::class, array(
              'label' => 'search.registration.registred',
                )
            )
            ->add('notRegistred', CheckboxType::class, array(
              'label' => 'search.registration.not_registred',
                )
            )
            ->add('extern', CheckboxType::class, array(
              'label' => 'search.student.extern',
                )
              )
            ->add('intren', CheckboxType::class, array(
              'label' => 'search.student.intren',
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
        return 'sms_paymentbundle_search';
    }


}
