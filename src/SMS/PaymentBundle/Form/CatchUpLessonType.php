<?php

namespace SMS\PaymentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SMS\PaymentBundle\Entity\CatchUpLesson;
use SMS\UserBundle\Entity\Professor;

use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use API\Form\Type\HiddenEntityType;
use Doctrine\ORM\EntityRepository;
use SMS\EstablishmentBundle\Entity\Establishment;

class CatchUpLessonType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $establishment = $options['establishment'];

      $builder
            ->add('typePaymentName' ,TextType::class , array(
                'label' => 'catchuplesson.field.catchUpLessonName')
            )
            ->add('price' ,TextType::class , array(
                'label' => 'paymenttype.field.price')
            )
            ->add('registrationFee' ,TextType::class , array(
                'label' => 'paymenttype.field.registrationFee')
            )
            ->add('description',TextareaType::class , array(
                'label' => 'catchuplesson.field.description')
            )
            ->add('professor' , EntityType::class , array(
                'class'         => Professor::class,
                'query_builder' => function (EntityRepository $er) use ($establishment) {
                    return $er->createQueryBuilder('professor')
                              ->join('professor.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'placeholder'   => 'catchuplesson.field.select_professor',
                'label'         => 'catchuplesson.field.professor',
                'attr'          => [ 'class'=> 'professor']
                )
            )
            ->add('establishment', HiddenEntityType::class, array(
                'class' => Establishment::class,
                'data' =>  $establishment, // Field value by default
                ))
            ->add('save', SubmitType::class, array ("label" => "md-fab"));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => CatchUpLesson::class,
            'allow_extra_fields' => true
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_paymentbundle_catchuplesson';
    }


}
