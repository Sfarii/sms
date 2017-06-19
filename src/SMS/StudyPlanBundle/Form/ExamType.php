<?php

namespace SMS\StudyPlanBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\StudyPlanBundle\Entity\Exam;
use SMS\StudyPlanBundle\Entity\TypeExam;
use SMS\StudyPlanBundle\Entity\Course;
use SMS\StudyPlanBundle\Entity\Session;
use SMS\EstablishmentBundle\Entity\Section;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use API\Form\EventSubscriber\GradeSectionCourseFilterListener;
use API\Form\Type\HiddenEntityType;
use Doctrine\ORM\EntityRepository;
use SMS\EstablishmentBundle\Entity\Establishment;

class ExamType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];
        $course = $options['course'];

        $builder
            ->add('section' , EntityType::class , array(
                    'class'         => Section::class,
                    'property'      => 'sectionName',
                    'query_builder' => function (EntityRepository $er) use ($course) {
                        return $er->createQueryBuilder('section')
                                  ->join('section.grade', 'grade')
                                  ->andWhere('grade.id = :grade')
                                  ->setParameter('grade', $course->getGrade()->getId());
                    },
                    'multiple' => true,
                    'placeholder'   => 'exam.field.section',
                    'label'         => 'exam.field.section',
                    )
                )
            ->add('examName' ,TextType::class , array(
                'label' => 'exam.field.examName')
            )
            ->add('dateExam', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'exam.field.dateExam' ,
                'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
            ))

            ->add('startTime', TimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'exam.field.startTime' ,
                'attr' => [ 'data-uk-timepicker'=> ""],
            ))
            ->add('endTime', TimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'exam.field.endTime' ,
                'attr' => [ 'data-uk-timepicker'=> ""],
            ))
            ->add('typeExam' , EntityType::class , array(
                'class' => TypeExam::class,
                'property' => 'typeExamName',
                'placeholder'=> 'exam.field.typeExam',
                'label' => 'exam.field.typeExam')
            )
            ->add('establishment', HiddenEntityType::class, array(
                'class' => Establishment::class,
                'data' =>  $establishment, // Field value by default
            ))
            ->add('course', HiddenEntityType::class, array(
                'class' => Course::class,
                'data' =>  $course, // Field value by default
            ))
            ->add('save', SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Exam::class
        ));
        $resolver->setRequired('establishment');
        $resolver->setRequired('course');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_studyplanbundle_exam';
    }


}
