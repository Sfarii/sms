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
use SMS\StoreBundle\Entity\OrderUser;

class OrderUserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    ->add('id' ,TextType::class , array(
        'label' => 'orderuser.field.id' ,
        'attr' => [ 'form.grid'=> "none"])
    )
    ->add('price' ,TextType::class , array(
        'label' => 'orderuser.field.price')
    )
    ->add('quantity' ,TextType::class , array(
        'label' => 'orderuser.field.quantity')
    )
    ->add('state' ,TextType::class , array(
        'label' => 'orderuser.field.state')
    )
    ->add('created', DateType::class, array(
        'widget' => 'single_text',
        'html5' => false,
        'label' => 'orderuser.field.created' ,
        'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
    ))
    ->add('updated', DateType::class, array(
        'widget' => 'single_text',
        'html5' => false,
        'label' => 'orderuser.field.updated' ,
        'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
    ))
    ->add('product' , EntityType::class , array(
        'label' => 'orderuser.field.product')
    )
    ->add('student' , EntityType::class , array(
        'label' => 'orderuser.field.student')
    )
    ->add('user' , EntityType::class , array(
        'label' => 'orderuser.field.user')
    )
    ->add('save', SubmitType::class);

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => OrderUser::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_storebundle_orderuser';
    }


}
