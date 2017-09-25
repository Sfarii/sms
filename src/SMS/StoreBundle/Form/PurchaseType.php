<?php

namespace SMS\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SMS\StoreBundle\Form\Type\PurchaseStatusType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use API\Form\Type\HiddenEntityType;
use SMS\EstablishmentBundle\Entity\Establishment;
use SMS\StoreBundle\Entity\Purchase;
use SMS\StoreBundle\Entity\Provider;

class PurchaseType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $establishment = $options['establishment'];

        $builder
          ->add('state' ,PurchaseStatusType::class , array(
              'label' => 'purchase.field.state')
          )
          ->add('purchaseDate', DateType::class, array(
              'widget' => 'single_text',
              'html5' => false,
              'label' => 'purchase.field.purchaseDate' ,
              'attr' => [ 'data-uk-datepicker'=> "{format:'YYYY-MM-DD'}"],
          ))
          ->add('provider' , EntityType::class , array(
              'class' => Provider::class ,
              "property" => "socialReason",

              'query_builder' => function ( $er) use ($establishment) {
                  return $er->createQueryBuilder('provider')
                            ->join('provider.establishment', 'establishment')
                            ->andWhere('establishment.id = :establishment')
                            ->setParameter('establishment', $establishment->getId());
              },
              "placeholder" => "purchase.field.select_provider",
              'label' => 'purchase.field.provider')
          )
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
            'data_class' => Purchase::class
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_storebundle_Purchase';
    }


}
