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
use SMS\StoreBundle\Entity\Delivery;

class DeliveryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    ->add('id' ,TextType::class , array(
        'label' => 'delivery.field.id' ,
        'attr' => [ 'form.grid'=> "none"])
    )
    ->add('quantity' ,TextType::class , array(
        'label' => 'delivery.field.quantity')
    )
    ->add('deliveryDate', DateType::class, array(
        'widget' => 'single_text',
        'html5' => false,
        'label' => 'delivery.field.deliveryDate' ,
        'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
    ))
    ->add('created', DateType::class, array(
        'widget' => 'single_text',
        'html5' => false,
        'label' => 'delivery.field.created' ,
        'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
    ))
    ->add('updated', DateType::class, array(
        'widget' => 'single_text',
        'html5' => false,
        'label' => 'delivery.field.updated' ,
        'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
    ))
    ->add('product' , EntityType::class , array(
        'label' => 'delivery.field.product')
    )
    ->add('provider' , EntityType::class , array(
        'label' => 'delivery.field.provider')
    )
    ->add('user' , EntityType::class , array(
        'label' => 'delivery.field.user')
    )
    ->add('save', SubmitType::class);

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Delivery::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_storebundle_delivery';
    }


}
