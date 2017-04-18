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
use SMS\StudyPlanBundle\Entity\Day;
use SMS\EstablishmentBundle\Entity\Grade;
use SMS\EstablishmentBundle\Entity\Section;
use SMS\UserBundle\Entity\Professor;

use API\Form\Type\DayType;
use API\Form\EventSubscriber\GradeSectionCourseFilterListener;

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
        
        $builder->addEventSubscriber(new GradeSectionCourseFilterListener($this->em))
                ->add('day' , DayType::class , array(
                    'placeholder'   => 'schedule.field.day',
                    'label'         => 'schedule.field.day')
                )
                ->add('sessions' , EntityType::class , array(
                    'class'         => Session::class,
                    'property'      => 'sessionName',
                    'placeholder'   => 'schedule.field.session',
                    'multiple'      => true,
                    'label'         => 'schedule.field.session')
                )
                ->add('professor' , EntityType::class , array(
                    'class'         => Professor::class,
                    'property'      => 'firstName',
                    'placeholder'   => 'schedule.field.professor',
                    'label'         => 'schedule.field.professor'
                    )
                )
                ->add('save', SubmitType::class);
    }
   
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Schedule::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_studyplanbundle_schedule';
    }


}
