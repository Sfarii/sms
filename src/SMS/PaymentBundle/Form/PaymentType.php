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
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;
use API\Form\Type\HiddenEntityType;
use SMS\PaymentBundle\Entity\PaymentType as TypePayment;

class PaymentType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var String Class Names
     */
    protected $studentClass;
    /**
     * Constructor
     *
     * @param String $studentClass
     */
    function __construct($studentClass , EntityManager $em)
    {
        $this->studentClass = $studentClass;
        $this->em = $em;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $student = $options['student'];
        $builder
          ->add('month' ,MonthType::class , array(
                'label' => 'payment.field.month',
                'placeholder'   => 'payment.field.month',
                'attr'          => [ 'class'=> 'monthField'])
            )
            ->add('price' ,TextType::class , array(
                'label' => 'payment.field.price')
            )
            ->add('paymentType' , EntityType::class , array(
                'class' => TypePayment::class ,
                'property' => 'typePaymentName',
                'query_builder' => function ($er) use ($student) {
                    return $er->createQueryBuilder('paymentType')
                              ->join('paymentType.student', 'registration')
                              ->andWhere('registration.id = :student')
                              ->setParameter('student', $student->getId());
                },
                'placeholder'   => 'payment.field.select_paymentType',
                'label' => 'payment.field.paymentType',
                'attr'          => [ 'class'=> 'paymentTypeField'])
            )
            ->add('paid' ,TextType::class , array(
                'label' => 'paymenttype.field.price',
                'mapped' => false ,
                'disabled' => true)
            )
            ->add('credit' ,TextType::class , array(
                'label' => 'payment.field.credit',
                'mapped' => false ,
                'disabled' => true)
            )
            ->add('student', HiddenEntityType::class, array(
                'class' => $this->studentClass,
                'data' =>  $student, // Field value by default
                ))
            ->addEventListener(
                FormEvents::PRE_SUBMIT,
                array($this, 'onPreSubmit')
            )->addEventListener(
                FormEvents::POST_SET_DATA,
                array($this, 'onPostSubmit')
            )
            ->add('save', SubmitType::class);

    }

    public function onPostSubmit(FormEvent $event)
    {
      $form = $event->getForm();
      $data = $event->getData();
      if (!is_null($data->getStudent()) || !is_null($data->getPaymentType()) || !is_null($data->getMonth())){
        $query = $this->em->getRepository($this->studentClass)->findRegistredStudent($data->getStudent()->getId() , $data->getPaymentType()->getId());
        $query->select(sprintf("registrations.price as price , (SELECT SUM(credit_payment.credit) FROM %s as credit_payment WHERE credit_payment.paymentType = registrations AND credit_payment.student = student AND credit_payment.month = %s ) AS credit", Payment::class , $data->getMonth()));
        $query->addSelect(sprintf("(SELECT SUM(paid_payment.price) FROM %s as paid_payment WHERE paid_payment.paymentType = registrations AND paid_payment.student = student AND paid_payment.month = %s ) AS paid", Payment::class , $data->getMonth()));
        $result = $query->getQuery()->getOneOrNullResult();
        if (is_null($result['credit']) || is_null($result['paid'])){
          $form->get('credit')->setData($result['price']);
          $form->get('paid')->setData('0');
        }else{
          $form->get('credit')->setData($result['credit']);
          $form->get('paid')->setData($result['paid']);
        }
      }
    }

    public function onPreSubmit(FormEvent $event)
    {
      $form = $event->getForm();
      $data = $event->getData();
      if (!empty($data['student']) && !empty($data['paymentType'])){
        $query = $this->em->getRepository($this->studentClass)->findRegistredStudent($data['student'] , $data['paymentType']);
        $query->select(sprintf("registrations.price as price , (SELECT SUM(credit_payment.credit) FROM %s as credit_payment WHERE credit_payment.paymentType = registrations AND credit_payment.student = student AND credit_payment.month = %s ) AS credit", Payment::class , $data['month']));
        $query->addSelect(sprintf("(SELECT SUM(paid_payment.price) FROM %s as paid_payment WHERE paid_payment.paymentType = registrations AND paid_payment.student = student AND paid_payment.month = %s ) AS paid", Payment::class , $data['month']));
        $result = $query->getQuery()->getOneOrNullResult();
        if (is_null($result['credit']) || is_null($result['paid'])){
          $form->get('credit')->setData($result['price']);
          $form->get('paid')->setData('0');
        }else{
          $form->get('credit')->setData($result['credit']);
          $form->get('paid')->setData($result['paid']);
        }

      }

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Payment::class
        ));
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
