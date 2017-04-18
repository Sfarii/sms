<?php

namespace SMS\StudyPlanBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\EstablishmentBundle\Entity\Division;
use SMS\UserBundle\Entity\Professor;
use Doctrine\ORM\EntityManager;
use API\Form\EventSubscriber\GradeSectionFilterListener;
use Symfony\Component\Validator\Constraints\NotBlank;

class ScheduleProfessorFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('division' , EntityType::class , array(
                'class' => Division::class,
                'property' => 'divisionName',
                'placeholder'=> 'filter.field.division',
                'constraints'   => [new NotBlank()],
                'label' => 'filter.field.division')
            )
            ->add('professor' , EntityType::class , array(
                    'class'         => Professor::class,
                    'placeholder'   => 'filter.field.professor',
                    'label'         => 'filter.field.professor',
                    'constraints'   => [new NotBlank()],
                    'attr'          => [ 'class'=> 'professorField'])
                
            )
            ->add('save', SubmitType::class ,array(
                    'label' => 'filter.field.send',
                    'attr' => [ "button_type" => "filter"]
                ));

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_study_plan_bundle_schedule_student_filter';
    }


}
