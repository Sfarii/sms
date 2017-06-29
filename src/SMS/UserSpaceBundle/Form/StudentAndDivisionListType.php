<?php

namespace SMS\UserSpaceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\EstablishmentBundle\Entity\Division;
use SMS\UserBundle\Entity\Student;
use SMS\UserBundle\Entity\StudentParent;
use Symfony\Component\Security\Core\SecurityContext;

class StudentAndDivisionListType extends AbstractType
{
    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @param SecurityContext $securityContext
     *
     */
    public function __construct(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $user = $this->securityContext->getToken()->getUser();
      $builder
          ->add('division' , EntityType::class , array(
              'class' => Division::class,
              'placeholder'=> 'filter.field.division',
              'label' => 'filter.field.division')
          )
          ->add('student' , EntityType::class , array(
              'class' => Student::class,
              'choices'       => $user->getStudents()->toArray(),
              'placeholder'=> 'filter.field.division',
              'label' => 'filter.field.division')
          )
          ->add('send', SubmitType::class ,array(
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
        return 'sms_user_space_bundle_section';
    }


}
