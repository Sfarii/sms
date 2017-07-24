<?php

namespace SMS\StudyPlanBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use SMS\StudyPlanBundle\Entity\Schedule;
use SMS\StudyPlanBundle\Entity\Session;
use SMS\StudyPlanBundle\Entity\Course;
use SMS\EstablishmentBundle\Entity\Section;
use SMS\UserBundle\Entity\Professor;
use SMS\EstablishmentBundle\Entity\Division;

use API\Form\Type\HiddenEntityType;
use Doctrine\ORM\EntityRepository;
use SMS\EstablishmentBundle\Entity\Establishment;

use API\Form\Type\DayType;
use SMS\StudyPlanBundle\Form\EventSubscriber\ScheduleFilterListener;

class ScheduleType extends AbstractType
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
        $establishment = $options['establishment'];
        $section = $options['section'];
        $division = $options['division'];


        $builder->add('day' , DayType::class , array(
                    'placeholder'   => 'schedule.field.select_day',
                    'label'         => 'schedule.field.day',
                    'attr'          => [ 'class'=> 'day']
                  )
                )
                ->add('sessions' , EntityType::class , array(
                    'class'         => Session::class,
                    'property'      => 'sessionName',

                    'multiple'      => true,
                    'query_builder' => function (EntityRepository $er) use ($establishment) {
                        return $er->createQueryBuilder('sessions')
                                  ->join('sessions.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'label' => 'schedule.field.session',
                    'choice_label' => function ($session) {
                        return sprintf("%s : %s => %s",$session->getSessionName(),$session->getStartTime()->format('H:i:s') ,$session->getEndTime()->format('H:i:s'));
                    },
                    'attr'          => [ 'class'=> 'sessions' ,'placeholder'   => 'schedule.field.select_session' ],)
                )
                ->add('course' , EntityType::class , array(
                            'class' => Course::class,
                            'property' => 'courseName',
                            'query_builder' => function (EntityRepository $er) use ($establishment, $section,$division) {
                                return $er->createQueryBuilder('course')
                                          ->join('course.grade', 'grade')
                                          ->join('course.establishment', 'establishment')
                                          ->join('course.division', 'division')
                                          ->andWhere('establishment.id = :establishment')
                                          ->setParameter('establishment', $establishment->getId())
                                          ->andWhere('grade.id = :grade')
                                          ->setParameter('grade', $section->getGrade()->getId())
                                          ->andWhere('division.id = :division')
                                          ->setParameter('division', $division->getId());
                            },
                            'placeholder'=> 'filter.field.select_course',
                            'label' => 'filter.field.course',
                            'attr'          => [ 'class'=> 'course'])
                        )
                ->add('professor' , EntityType::class , array(
                    'class'         => Professor::class,
                    'query_builder' => function (EntityRepository $er) use ($establishment) {
                        return $er->createQueryBuilder('professor')
                                  ->join('professor.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'placeholder'   => 'filter.field.select_professor',
                    'label'         => 'schedule.field.professor',
                    'attr'          => [ 'class'=> 'professor']
                    )
                )
                ->add('establishment', HiddenEntityType::class, array(
                    'class' => Establishment::class,
                    'data' =>  $establishment, // Field value by default
                    'attr'          => [ 'class'=> 'establishment']
                ))
                ->add('division', HiddenEntityType::class, array(
                    'class' => Division::class,
                    'data' =>  $division, // Field value by default
                    'attr'          => [ 'class'=> 'division']
                ))
                ->add('section', HiddenEntityType::class, array(
                    'class' => Section::class,
                    'data' =>  $section, // Field value by default
                    'attr'          => [ 'class'=> 'section']
                ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Schedule::class,
        ));
        $resolver->setRequired('establishment');
        $resolver->setRequired('section');
        $resolver->setRequired('division');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_studyplanbundle_schedule';
    }


}
