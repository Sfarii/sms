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
use SMS\StoreBundle\Entity\Invoice;

class InvoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    ->add('id' ,TextType::class , array(
        'label' => 'invoice.field.id' ,
        'attr' => [ 'form.grid'=> "none"])
    )
    ->add('reference' ,TextType::class , array(
        'label' => 'invoice.field.reference')
    )
    ->add('created', DateType::class, array(
        'widget' => 'single_text',
        'html5' => false,
        'label' => 'invoice.field.created' ,
        'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
    ))
    ->add('updated', DateType::class, array(
        'widget' => 'single_text',
        'html5' => false,
        'label' => 'invoice.field.updated' ,
        'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
    ))
    ->add('delivery' , EntityType::class , array(
        'label' => 'invoice.field.delivery')
    )
    ->add('user' , EntityType::class , array(
        'label' => 'invoice.field.user')
    )
    ->add('save', SubmitType::class);

    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Invoice::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_storebundle_invoice';
    }


}
