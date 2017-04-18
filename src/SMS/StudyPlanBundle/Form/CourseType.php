<?php

namespace SMS\StudyPlanBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\StudyPlanBundle\Entity\Course;
use SMS\EstablishmentBundle\Entity\Division;
use SMS\EstablishmentBundle\Entity\Grade;
use Doctrine\ORM\EntityManager;

class CourseType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('courseName' ,TextType::class , array(
                'label' => 'course.field.courseName')
            )
            ->add('coefficient' ,TextType::class , array(
                'label' => 'course.field.coefficient')
            )
            ->add('division' , EntityType::class , array(
                'class' => Division::class,
                'property' => 'divisionName',
                'placeholder'=> 'course.field.division',
                'label' => 'course.field.division')
            )
            ->add('grade' , EntityType::class , array(
                'class' => Grade::class,
                'property' => 'gradeName',
                'placeholder'=> 'course.field.grade',
                'label' => 'course.field.grade')
            )
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
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_studyplanbundle_course';
    }


}
