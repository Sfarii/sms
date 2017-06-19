<?php

namespace SMS\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use SMS\UserBundle\Entity\Professor;
use Vich\UploaderBundle\Form\Type\VichImageType;
use API\Form\Type\GenderType;
use API\Form\EventSubscriber\UsersListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfessorType extends AbstractType
{

  /**
   * @var TokenStorageInterface
   */
  private $tokenStorage;

  /**
   * Constructor
   *
   * @param EntityManager $em
   */
  function __construct(TokenStorageInterface $tokenStorage)
  {
      $this->tokenStorage = $tokenStorage;
  }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('imageFile',  VichImageType::class, array(
                    'allow_delete' => false, // not mandatory, default is true
                    'download_link' => false, // not mandatory, default is true
                    'label' => false ,
                ))
                ->add('firstName' ,TextType::class , array(
                    'label' => 'professor.field.firstName')
                )
                ->add('lastName' ,TextType::class , array(
                    'label' => 'professor.field.lastName')
                )
                ->add('gender' ,GenderType::class , array(
                    'label' => 'user.field.gender')
                )
                ->add('birthday', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'label' => 'professor.field.birthday' ,
                    'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
                ))
                ->add('phone' ,TextType::class , array(
                    'label' => 'professor.field.phone')
                )
                ->add('address' ,TextType::class , array(
                    'label' => 'professor.field.address')
                )
                ->add('grade' ,TextType::class , array(
                    'label' => 'professor.field.grade')
                )
                ->add('email' ,TextType::class , array(
                    'label' => 'professor.field.email')
                )
                ->add('show_username_password', CheckboxType::class, array(
                    'label' => 'user.field.show_username_password',
                    'mapped' => false
                    )
                )
                ->addEventSubscriber(new UsersListener($this->tokenStorage))
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
            'data_class' => Professor::class,
            'allow_extra_fields' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_userbundle_professor';
    }


}
