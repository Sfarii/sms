<?php

namespace SMS\EstablishmentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use SMS\EstablishmentBundle\Entity\Division;
use API\Form\Type\HiddenEntityType;
use SMS\EstablishmentBundle\Entity\Establishment;

class DivisionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];

        $builder
                ->add('divisionName' ,TextType::class , array(
                    'label' => 'division.field.divisionName')
                )
                ->add('startDate', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'label' => 'division.field.startDate' ,
                    'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
                ))
                ->add('endDate', DateType::class, array(
                    'widget' => 'single_text',
                    'html5' => false,
                    'label' => 'division.field.endDate' ,
                    'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
                ))
                ->add('establishment', HiddenEntityType::class, array(
                    'class' => Establishment::class,
                    'data' =>  $establishment, // Field value by default
                    ))
                ->add('save', SubmitType::class);

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Division::class
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_establishmentbundle_division';
    }


}
