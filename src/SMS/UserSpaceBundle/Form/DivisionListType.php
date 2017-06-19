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

class DivisionListType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('division' , EntityType::class , array(
                'class' => Division::class,
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
