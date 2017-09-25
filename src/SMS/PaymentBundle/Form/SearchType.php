<?php

namespace SMS\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use SMS\PaymentBundle\Entity\PaymentType as TypePayment;
use Doctrine\ORM\EntityManager;
use API\Form\Type\HiddenEntityType;
use SMS\PaymentBundle\Form\Type\GenderType;
use API\Form\Type\MonthType;
use SMS\EstablishmentBundle\Entity\Establishment;

class SearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $establishment = $options['establishment'];

        $builder
              ->add('textField' ,TextType::class , array(
                  'label' => 'search.student.by_every_thing')
              )
              ->add('gender' ,GenderType::class , array(
                  'label' => 'user.field.gender')
              )
              ->add('birthday', TextType::class, array(
                  'label' => 'student.field.birthday' ,
                  'attr' => [ 'class' => 'birthday'],
              ))
              ->add('status', ChoiceType::class, array(
                'choices'  => array(
                    'extern' => 'search.student.extern',
                    'intren' => 'search.student.intren',
                ),
                'placeholder'   => 'search.student.status'
            ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_paymentbundle_search';
    }


}
