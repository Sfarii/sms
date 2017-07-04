<?php

namespace SMS\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\PaymentBundle\Entity\Payment;
use API\Form\Type\MonthType;
use API\Form\Type\HiddenEntityType;
use Doctrine\ORM\EntityManager;
use SMS\EstablishmentBundle\Entity\Establishment;
use SMS\PaymentBundle\Entity\PaymentType as TypePayment;

class PaymentType extends AbstractType
{
    /**
     * @var String Class Names
     */
    protected $studentClass;
    protected $establishmentClass;
    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct($studentClass , $establishmentClass)
    {
        $this->studentClass = $studentClass;
        $this->establishmentClass =  $establishmentClass;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];
        $student = $options['student'];
        $builder
          ->add('month' ,MonthType::class , array(
                'label' => 'payment.field.month',
                'placeholder'   => 'payment.field.month')
            )
            ->add('price' ,TextType::class , array(
                'label' => 'payment.field.price')
            )
            ->add('paymentType' , EntityType::class , array(
                'class' => TypePayment::class ,
                'property' => "TypePaymentName",
                'query_builder' => function ($er) use ($establishment) {
                    return $er->createQueryBuilder('paymentType')
                              ->join('paymentType.registration', 'registration')
                              ->join('registration.student', 'student')
                              ->andWhere('student.id = :student')
                              ->setParameter('student', $student->getId());
                },
                'placeholder'   => 'payment.field.select_paymentType',
                'label' => 'payment.field.paymentType',
                'attr'          => [ 'class'=> 'paymentTypeField'])
            )
            ->add('establishment', HiddenEntityType::class, array(
                'class' => $this->establishmentClass,
                'data' =>  $establishment, // Field value by default
                ))
            ->add('student', HiddenEntityType::class, array(
                'class' => $this->studentClass,
                'data' =>  $student, // Field value by default
                ))
            ->add('save', SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Payment::class
        ));
        $resolver->setRequired('establishment');
        $resolver->setRequired('student');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_paymentbundle_payment';
    }


}
