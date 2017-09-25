<?php

namespace SMS\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use API\Form\Type\HiddenEntityType;
use SMS\EstablishmentBundle\Entity\Establishment;
use SMS\StoreBundle\Entity\Provider;
use SMS\UserBundle\Entity\Student;
use SMS\UserBundle\Entity\StudentParent;
use SMS\UserBundle\Entity\Professor;

class OrderType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $establishment = $options['establishment'];
        $builder
            ->add('provider' , EntityType::class , array(
                'class' => Provider::class ,
                "property" => "socialReason",

                'query_builder' => function ( $er) use ($establishment) {
                    return $er->createQueryBuilder('provider')
                              ->join('provider.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'multiple' => true ,
                'attr' => ["placeholder" => "order.field.provider", 'selectize' => 'none'],
                'label' => 'order.field.provider')
            )
            ->add('student' , EntityType::class , array(
                'class' => Student::class ,

                'query_builder' => function ( $er) use ($establishment) {
                    return $er->createQueryBuilder('student')
                              ->join('student.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'attr' => [ "placeholder" => "order.field.student",'selectize' => 'none' ],
                'multiple' => true ,
                'label' => 'order.field.student')
            )
            ->add('professor' , EntityType::class , array(
                'class' => Professor::class ,

                'query_builder' => function ( $er) use ($establishment) {
                    return $er->createQueryBuilder('professor')
                              ->join('professor.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'attr' => [ "placeholder" => "order.field.professor", 'selectize' => 'none'],
                'multiple' => true ,
                'label' => 'order.field.professor')
            )
            ->add('establishment', HiddenEntityType::class, array(
                'class' => Establishment::class,
                'data' =>  $establishment, // Field value by default
                ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setRequired('establishment');
        $resolver->setDefaults(array(
            'allow_extra_fields' => true
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_storebundle_order';
    }



}
