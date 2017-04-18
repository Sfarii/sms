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
use Doctrine\ORM\EntityManager;
use API\Form\EventSubscriber\GradeSectionFilterListener;
use Symfony\Component\Validator\Constraints\NotBlank;

class ScheduleStudentFilterType extends AbstractType
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
            ->addEventSubscriber(new GradeSectionFilterListener($this->em))
            ->add('division' , EntityType::class , array(
                'class' => Division::class,
                'property' => 'divisionName',
                'placeholder'=> 'filter.field.division',
                'constraints'   => [new NotBlank()],
                'label' => 'filter.field.division',
                'attr'          => [ 'class'=> 'divisionField'])
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
