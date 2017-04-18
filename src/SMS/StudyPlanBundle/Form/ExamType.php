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
use SMS\StudyPlanBundle\Entity\Session;
use API\Form\EventSubscriber\GradeCourseFilterListener;

class ExamType extends AbstractType
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
            ->addEventSubscriber(new GradeCourseFilterListener($this->em))
            ->add('examName' ,TextType::class , array(
                'label' => 'exam.field.examName')
            )
            ->add('factor' ,TextType::class , array(
                'label' => 'exam.field.factor')
            )
            ->add('dateExam', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'exam.field.dateExam' ,
                'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
            ))
    
            ->add('typeExam' , EntityType::class , array(
                'class' => TypeExam::class,
                'property' => 'typeExamName',
                'placeholder'=> 'exam.field.typeExam',
                'label' => 'exam.field.typeExam')
            )
            ->add('sessions' , EntityType::class , array(
                'class' => Session::class,
                'property' => 'sessionName',
                'multiple' => true,
                'placeholder'=> 'exam.field.session',
                'label' => 'exam.field.session')
            )
            
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
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_studyplanbundle_exam';
    }


}
