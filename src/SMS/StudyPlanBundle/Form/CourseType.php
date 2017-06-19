<?php

namespace SMS\StudyPlanBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\StudyPlanBundle\Entity\Course;
use SMS\EstablishmentBundle\Entity\Division;
use SMS\EstablishmentBundle\Entity\Grade;
use Doctrine\ORM\EntityManager;
use API\Form\Type\HiddenEntityType;
use Doctrine\ORM\EntityRepository;
use SMS\EstablishmentBundle\Entity\Establishment;

class CourseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];

        $builder
            ->add('courseName' ,TextType::class , array(
                'label' => 'course.field.courseName')
            )
            ->add('coefficient' ,IntegerType::class , array(
                'label' => 'course.field.coefficient')
            )
            ->add('division' , EntityType::class , array(
                'class' => Division::class,
                'property' => 'divisionName',
                'query_builder' => function (EntityRepository $er) use ($establishment) {
                    return $er->createQueryBuilder('division')
                              ->join('division.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'placeholder'=> 'course.field.division',
                'label' => 'course.field.division')
            )
            ->add('grade' , EntityType::class , array(
                'class' => Grade::class,
                'property' => 'gradeName',
                'query_builder' => function (EntityRepository $er) use ($establishment) {
                    return $er->createQueryBuilder('grade')
                              ->join('grade.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'placeholder'=> 'course.field.grade',
                'label' => 'course.field.grade')
            )
            ->add('establishment', HiddenEntityType::class, array(
                'class' => Establishment::class,
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
            'data_class' => Course::class
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_studyplanbundle_course';
    }


}
