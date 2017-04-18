<?php

namespace SMS\AdministrativeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\AdministrativeBundle\Entity\AttendanceProfessor;
use API\Form\EventSubscriber\ProfessorAttendanceFilterListener;

class ProfessorAttendanceType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new ProfessorAttendanceFilterListener($this->em))
            ->add('status' ,CheckboxType::class , array(
                'label' => 'attendance.field.status',
                'attr' => ['data-switchery'=> ""])
            )
            ->add('save', SubmitType::class);

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => AttendanceProfessor::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_administrativebundle_attendance';
    }


}
