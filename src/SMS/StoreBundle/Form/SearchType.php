<?php

namespace SMS\StoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use API\Form\Type\HiddenEntityType;
use SMS\EstablishmentBundle\Entity\Establishment;
use SMS\StoreBundle\Entity\Product;
use SMS\StoreBundle\Entity\ProductType as TypeProduct;
use Vich\UploaderBundle\Form\Type\VichImageType;

class SearchType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $establishment = $options['establishment'];
        $builder
            ->add('textField' ,TextType::class , array(
                'label' => 'product.field.productName')
            )
            ->add('price' ,TextType::class , array(
                'label' => 'product.field.price',
              'attr' => [ 'readonly'=> 'true' ,  'class' => 'number_range_price'])
            )
            ->add('stock' ,TextType::class , array(
                'label' => 'product.field.stock',
                'attr' => [ 'readonly'=> 'true' , 'class' => 'number_range_stock'])
            )
            ->add('productType' , EntityType::class , array(
                "class" => TypeProduct::class,
                'multiple' => true ,
                'property' => "productTypeName",
                'query_builder' => function ( $er) use ($establishment) {
                    return $er->createQueryBuilder('productType')
                              ->join('productType.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'attr' => [ 'placeholder' => 'product.field.productType' ])
            );

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_storebundle_search';
    }
}
