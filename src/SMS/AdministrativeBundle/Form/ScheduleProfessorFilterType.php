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
use Symfony\Component\Validator\Constraints\NotBlank;

class ScheduleProfessorFilterType extends AbstractType
{
    /**
     * @var String Class Names
     */
    protected $divisionClass;
    protected $professorClass;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em , $divisionClass , $professorClass )
    {
        $this->em = $em;
        $this->professorClass = $professorClass;
        $this->divisionClass = $divisionClass;
    }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];

        $builder
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
                'label' => 'schedule.field.division')
            )
            ->add('professor' , EntityType::class , array(
                    'class'         => $this->professorClass,
                    'query_builder' => function (EntityRepository $er) use ($establishment) {
                        return $er->createQueryBuilder('professor')
                                  ->join('professor.establishment', 'establishment')
                                  ->andWhere('establishment.id = :establishment')
                                  ->setParameter('establishment', $establishment->getId());
                    },
                    'placeholder'   => 'schedule.field.professor',
                    'label'         => 'schedule.field.professor',
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
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_study_plan_bundle_schedule_division_filter';
    }


}
