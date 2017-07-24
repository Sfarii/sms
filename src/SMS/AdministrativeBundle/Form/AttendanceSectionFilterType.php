<?php

namespace SMS\AdministrativeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Date;

class AttendanceSectionFilterType extends AbstractType
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var String Class Names
     */
    protected $sessionClass;
    protected $courseClass;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em , $sessionClass , $courseClass)
    {
        $this->em = $em;
        $this->sessionClass = $sessionClass;
        $this->courseClass = $courseClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];
        $grade = $options['grade'];

        $builder
            ->add('session' , EntityType::class , array(
                'class' => $this->sessionClass,
                'property' => 'sessionName' ,
                'query_builder' => function (EntityRepository $er) use ($establishment) {
                    return $er->createQueryBuilder('session')
                              ->join('session.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'multiple' => true,
                'attr'          => [ 'placeholder'=> 'filter.field.select_division'])
            )
            ->add('course' , EntityType::class , array(
                'class' => $this->courseClass,
                'query_builder' => function (EntityRepository $er) use ($establishment , $grade) {
                    return $er->createQueryBuilder('course')
                              ->join('course.establishment', 'establishment')
                              ->join('course.grade', 'grade')
                              ->andWhere('grade.id = :grade')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('grade', $grade->getId())
                              ->setParameter('establishment', $establishment->getId());
                },
                'choice_label' => function ($course) {
                    return sprintf("%s %s",$course->getCourseName(),$course->getDivision()->getDivisionName());
                },
                'multiple' => true,
                'attr'          => [ 'placeholder'=> 'filter.field.select_course'])
            )
            ->add('startDate', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'constraints'   => [new Date()],
                'label' => 'daterange.start_date' ,
                'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
            ))
            ->add('endDate', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'constraints'   => [new Date()],
                'label' => 'daterange.end_date' ,
                'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
            ))

            ->add('save', SubmitType::class ,array(
                    'label' => 'filter.field.new',
                    'attr' => [ 'button_type' => 'filter' , 'icon' => 'search']
                ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('establishment');
        $resolver->setRequired('grade');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_study_plan_bundle_attendance_section_filter';
    }


}
