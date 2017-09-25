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
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      $establishment = $options['establishment'];
        $builder
            ->add('imageFile',  VichImageType::class, array(
                    'allow_delete' => false, // not mandatory, default is true
                    'download_link' => false, // not mandatory, default is true
                    'label' => false )
                )
            ->add('productName' ,TextType::class , array(
                'label' => 'product.field.productName')
            )
            ->add('price' ,TextType::class , array(
                'label' => 'product.field.price')
            )
            ->add('description' ,TextareaType::class , array(
                'label' => 'product.field.description')
            )
            ->add('active' , CheckboxType::class , array(
                'label' => 'product.field.active')
            )
            ->add('productType' , EntityType::class , array(
                "class" => TypeProduct::class,
                'property' => "productTypeName",
                'query_builder' => function ( $er) use ($establishment) {
                    return $er->createQueryBuilder('productType')
                              ->join('productType.establishment', 'establishment')
                              ->andWhere('establishment.id = :establishment')
                              ->setParameter('establishment', $establishment->getId());
                },
                'placeholder' => 'product.field.select_productType',
                'label' => 'product.field.productType')
            )
            ->add('establishment', HiddenEntityType::class, array(
                'class' => Establishment::class,
                'data' =>  $establishment, // Field value by default
                ))
            ->add('save', SubmitType::class  , array ("label" => "md-fab"));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Product::class
        ));
        $resolver->setRequired('establishment');
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sms_storebundle_product';
    }


}
