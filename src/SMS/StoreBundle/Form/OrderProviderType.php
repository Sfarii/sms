<?php

namespace SMS\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SMS\StoreBundle\Form\Type\StatusType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use API\Form\Type\HiddenEntityType;
use SMS\EstablishmentBundle\Entity\Establishment;
use SMS\StoreBundle\Entity\OrderProvider;
use SMS\StoreBundle\Entity\Provider;

class OrderProviderType extends AbstractType
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
              'attr' => ["placeholder" => "orderprovider.field.provider"],
              'label' => 'orderprovider.field.provider')
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
            'data_class' => OrderProvider::class
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_storebundle_order_provider';
    }


}
