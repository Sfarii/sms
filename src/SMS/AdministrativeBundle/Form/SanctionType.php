<?php

namespace SMS\AdministrativeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\AdministrativeBundle\Entity\Sanction;
use SMS\UserBundle\Entity\Student;
use Doctrine\ORM\EntityManager;
use SMS\AdministrativeBundle\Form\EventSubscriber\GradeSectionStudentFilterListener;

class SanctionType extends AbstractType
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var String Class Names
     */
    protected $studentClass;
    protected $gradeClass;
    protected $sectionClass;

    /**
     * Constructor
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em , $studentClass , $gradeClass , $sectionClass)
    {
        $this->em = $em;
        $this->gradeClass = $gradeClass;
        $this->sectionClass = $sectionClass;
        $this->studentClass = $studentClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];
        $builder
            ->addEventSubscriber(new GradeSectionStudentFilterListener($this->em ,$this->studentClass , $this->gradeClass , $this->sectionClass , $establishment))
            ->add('punishment' ,TextType::class , array(
                'label' => 'sanction.field.punishment')
            )
            ->add('cause' ,TextareaType::class , array(
                'label' => 'sanction.field.cause')
            )
            ->add('save', SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Sanction::class
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_administrativebundle_sanction';
    }


}
