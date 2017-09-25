<?php

namespace SMS\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SMS\StoreBundle\Form\Type\OrderStatusType;
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
        $establishment = $options['establishment'];

        $builder
          ->add('state' ,OrderStatusType::class , array(
              'label' => 'orderuser.field.state')
          )
          ->add('orderDate', DateType::class, array(
              'widget' => 'single_text',
              'html5' => false,
              'label' => 'orderuser.field.orderDate' ,
              'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
          ))
          ->add('establishment', HiddenEntityType::class, array(
              'class' => Establishment::class,
              'data' =>  $establishment, // Field value by default
              ))
          ->add('save', SubmitType::class , array ("label" => "md-fab"));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => OrderUser::class
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_storebundle_Order_User';
    }


}
