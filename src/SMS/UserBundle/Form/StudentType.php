<?php

namespace SMS\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use SMS\UserBundle\Entity\Student;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Vich\UploaderBundle\Form\Type\VichImageType;
use SMS\UserBundle\Form\Type\GenderType;
use SMS\UserBundle\Form\EventSubscriber\StudentListener;
use SMS\UserBundle\Form\EventSubscriber\UsersListener;
use Doctrine\ORM\EntityRepository;
use SMS\UserBundle\Form\EventSubscriber\GradeSectionFilterListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class StudentType extends AbstractType
{
  /**
   * @var String Class Names
   */
  protected $gradeClass;
  protected $sectionClass;

  /**
   * @var TokenStorageInterface
   */
  private $tokenStorage;

  /**
   * @var EntityManager
   */
  protected $em;

  /**
   * Constructor
   *
   * @param EntityManager $em
   */
  function __construct(TokenStorageInterface $tokenStorage ,EntityManager $em  , $gradeClass , $sectionClass)
  {
      $this->tokenStorage = $tokenStorage;
      $this->em = $em;
      $this->gradeClass = $gradeClass;
      $this->sectionClass = $sectionClass;
  }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];

        $builder
            ->add('imageFile',  VichImageType::class, array(
                    'allow_delete' => false, // not mandatory, default is true
                    'download_link' => false, // not mandatory, default is true
                    'label' => false ,
                    'attr' => [ 'form.grid'=> "none"])
                )
            ->add('firstName' ,TextType::class , array(
                'label' => 'student.field.firstName')
            )
            ->add('lastName' ,TextType::class , array(
                'label' => 'student.field.lastName')
            )
            ->add('gender' ,GenderType::class , array(
                'label' => 'user.field.gender')
            )
            ->add('birthday', DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'label' => 'student.field.birthday' ,
                'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
            ))
            ->add('phone' ,TextType::class , array(
                'label' => 'student.field.phone')
            )
            ->add('email' ,TextType::class , array(
                'label' => 'user.field.email')
            )
            ->add('show_username_password', CheckboxType::class, array(
                'label' => 'user.field.show_username_password',
                'mapped' => false
                )
            )
            ->add('studentType', CheckboxType::class, array(
                'label' => 'student.field.studentType',
                'attr'  => [ 'class'=> 'studentTypeField']
                )
            )
            ->addEventSubscriber(new UsersListener($this->tokenStorage))
            ->addEventSubscriber(new GradeSectionFilterListener($this->em , $this->gradeClass , $this->sectionClass , $establishment))
            ->addEventSubscriber(new StudentListener($establishment))
            ->add('save', SubmitType::class ,array(
                'validation_groups' => "SimpleRegistration",
                'label' => 'md-fab'
            ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Student::class,
            'allow_extra_fields' => true
        ));

        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_userbundle_student';
    }


}
