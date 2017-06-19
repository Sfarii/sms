<?php

namespace SMS\AdministrativeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use SMS\AdministrativeBundle\Form\EventSubscriber\GradeSectionFilterListener;
use Symfony\Component\Validator\Constraints\NotBlank;

class ScheduleStudentFilterType extends AbstractType
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var String Class Names
     */
    protected $gradeClass;
    protected $sectionClass;
    protected $divisionClass;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em , $gradeClass , $sectionClass,$divisionClass)
    {
        $this->em = $em;
        $this->gradeClass = $gradeClass;
        $this->sectionClass = $sectionClass;
        $this->divisionClass = $divisionClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];

        $builder
            ->addEventSubscriber(new GradeSectionFilterListener($this->em , $establishment, $this->gradeClass , $this->sectionClass))
            ->add('division' , EntityType::class , array(
                'class' => $this->divisionClass,
                'query_builder' => function (EntityRepository $er) use ($establishment) {
                    return $er->createQueryBuilder('division')
                              ->join('division.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'placeholder'=> 'schedule.field.division',
                'constraints'   => [new NotBlank()],
                'label' => 'schedule.field.division',
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
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_study_plan_bundle_schedule_student_filter';
    }


}
