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
use API\Form\EventSubscriber\GradeSectionStudentFilterListener;

class SanctionType extends AbstractType
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
            ->addEventSubscriber(new GradeSectionStudentFilterListener($this->em))
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
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_administrativebundle_sanction';
    }


}
