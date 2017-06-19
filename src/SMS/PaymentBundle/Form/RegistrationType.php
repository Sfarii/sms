<?php

namespace SMS\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\PaymentBundle\Entity\Registration;
use SMS\PaymentBundle\Entity\RegistrationType as TypeRegistration;
use SMS\UserBundle\Entity\Student;
use Doctrine\ORM\EntityManager;
use SMS\PaymentBundle\Form\EventSubscriber\GradeSectionStudentFilterListener;
use API\Form\Type\HiddenEntityType;
use SMS\EstablishmentBundle\Entity\Establishment;

class RegistrationType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var String Class Names
     */
    protected $studentClass;
    protected $gradeClass;
    protected $sectionClass;
    protected $establishmentClass;
    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em , $studentClass , $gradeClass , $sectionClass , $establishmentClass)
    {
        $this->em = $em;
        $this->gradeClass = $gradeClass;
        $this->sectionClass = $sectionClass;
        $this->studentClass = $studentClass;
        $this->establishmentClass =  $establishmentClass;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $establishment = $options['establishment'];

        $builder
            ->addEventSubscriber(new GradeSectionStudentFilterListener($this->em ,$this->studentClass , $this->gradeClass , $this->sectionClass , $establishment))

            ->add('registered' ,CheckboxType::class , array(
                'label' => 'registration.field.registered')
            )
            ->add('registrationType' , EntityType::class , array(
                'class' => TypeRegistration::class ,
                'property' => "registrationTypeName",
                'query_builder' => function ( $er) use ($establishment) {
                    return $er->createQueryBuilder('provider')
                              ->join('provider.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'placeholder'   => 'registration.field.select_registrationType',
                'label' => 'registration.field.registrationType')
            )

            ->add('establishment', HiddenEntityType::class, array(
                'class' => $this->establishmentClass,
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
            'data_class' => Registration::class
        ));
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
