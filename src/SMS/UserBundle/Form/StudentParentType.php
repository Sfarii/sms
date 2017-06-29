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
use SMS\UserBundle\Entity\StudentParent;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use SMS\UserBundle\Form\EventSubscriber\UsersListener;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class StudentParentType extends AbstractType
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
  public function __construct(TokenStorageInterface $tokenStorage)
  {
      $this->tokenStorage = $tokenStorage;
  }
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imageFile', VichImageType::class, array(
                    'allow_delete' => false, // not mandatory, default is true
                    'download_link' => false, // not mandatory, default is true
                    'label' => false ,
                    'attr' => [ 'form.grid'=> "none"])
                )
            ->add('fatherName', TextType::class, array(
                'label' => 'studentparent.field.fatherName')
            )
            ->add('motherName', TextType::class, array(
                'label' => 'studentparent.field.motherName')
            )
            ->add('familyName', TextType::class, array(
                'label' => 'studentparent.field.familyName')
            )
            ->add('fatherProfession', TextType::class, array(
                'label' => 'studentparent.field.fatherProfession')
            )
            ->add('motherProfession', TextType::class, array(
                'label' => 'studentparent.field.motherProfession')
            )
            ->add('address', TextType::class, array(
                'label' => 'studentparent.field.address')
            )
            ->add('phone', TextType::class, array(
                'label' => 'studentparent.field.phone')
            )
            ->add('email', TextType::class, array(
                'label' => 'user.field.email')
            )
            ->add('show_username_password', CheckboxType::class, array(
                'label' => 'user.field.show_username_password',
                'mapped' => false
                )
            )
            ->addEventSubscriber(new UsersListener($this->tokenStorage))
            ->add('save', SubmitType::class, array(
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
            'data_class' => StudentParent::class,
            'allow_extra_fields' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_userbundle_studentparent';
    }
}
