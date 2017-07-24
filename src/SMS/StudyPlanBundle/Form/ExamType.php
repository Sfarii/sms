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
      $section = $options['section'];
      $division = $options['division'];

        $builder
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
              ->add('typeExam' , EntityType::class , array(
                  'class'         => TypeExam::class,
                  'property' => 'typeExamName',
                  'query_builder' => function (EntityRepository $er) use ($establishment) {
                      return $er->createQueryBuilder('typeExam')
                                ->join('typeExam.establishment', 'establishment')
                                ->andWhere('establishment.id = :establishment')
                                ->setParameter('establishment', $establishment->getId());
                  },
                  'placeholder'   => 'exam.field.select_typeExam',
                  'label'         => 'exam.field.typeExam',
                  'attr'          => [ 'class'=> 'typeExam']
                  )
              )

              ->add('examName' ,TextType::class , array(
                  'label' => 'exam.field.examName',
                  'attr'  => [ 'class'=> 'examName']
                )
              )
            ->add('dateExam', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'exam.field.dateExam' ,
                'attr' => [ 'class'=> 'dateExam' , 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
              )
            )
            ->add('startTime', TimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'exam.field.startTime' ,
                'attr' => [ 'data-uk-timepicker'=> "", 'class' => 'startTime'],
            ))
            ->add('endTime', TimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'exam.field.endTime' ,
                'attr' => [ 'data-uk-timepicker'=> "" , 'class' => 'endTime'],
            ))
            ->add('establishment', HiddenEntityType::class, array(
                'class' => Establishment::class,
                'data' =>  $establishment, // Field value by default
                'attr'          => [ 'class'=> 'establishment']
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
            'data_class' => Exam::class
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
        return 'sms_studyplanbundle_exam';
    }


}
